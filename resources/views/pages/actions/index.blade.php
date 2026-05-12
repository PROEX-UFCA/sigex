@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      @can('adicionar_ação')
      <a href="{{route('actions.create')}}" class="btn">Inserir</a>
      @endcan
      @can('importar_ações')
      <bottom class="btn" data-bs-toggle="modal" data-bs-target="#importar" aria-expanded="false"
        aria-controls="modalExample">Importar</bottom>
      <x-modal.modal id="importar" class="modal-center" title="Importar dados" route="{{route('actions.import')}}" textBtnClose="Cancelar"
        textBtnSave="Importar" classBtnSave="btn-primary">
        <x-slot:content>
          <h3>Atenção para a Importação de Dados</h3>
          <p>Para importar os dados corretamente, o seu arquivo .csv deve conter as
            seguintes colunas, na ordem exata especificada abaixo:</p>
          <div class="mb-2">
            <span class="badge badge-dark mb-1">ID Projeto</span>
            <span class="badge badge-dark mb-1">Título</span>
            <span class="badge badge-dark mb-1">Coordenador</span>
            <span class="badge badge-dark mb-1">SIAPE</span>
            <span class="badge badge-dark mb-1">Centro/Departamento</span>
            <span class="badge badge-dark mb-1">Data Inicio</span>
            <span class="badge badge-dark mb-1">Data Fim</span>
            <span class="badge badge-dark mb-1">Ano</span>
            <span class="badge badge-dark mb-1">Tipo Ação</span>
            <span class="badge badge-dark mb-1">Area Tematica</span>
            <span class="badge badge-dark mb-1">Modalidade</span>
          </div>
          <p><strong class="text-danger">Importante:</strong> Certifique-se de que o cabeçalho do seu arquivo .csv
            corresponda exatamente a estes nomes para evitar erros durante o processo de importação.</p>
          <div id="drop-area"
            class="rounded-4 d-flex flex-column justify-content-center align-items-center bg-light p-4 text-center"
            style="height: 150px; cursor: pointer; border: dashed 2px gray">
            <p class="text-muted mb-2">Arraste o .csv aqui ou clique para selecionar</p>
            <p class="text-red mb-2">Máximo 10MB</p>
            <input type="file" name="csv" id="csv" accept=".csv" required hidden>
            <div id="file-info" class="text-muted small mt-2"></div>
          </div>
        </x-slot:content>
      </x-modal.modal>
      @endcan
      <bottom class="btn" data-bs-toggle="collapse" data-bs-target="#filtros" aria-expanded="false"
        aria-controls="collapseExample">Filtros</bottom>
    </div>
    <div class="d-flex justify-content-end col-12 col-md-6 p-0 m-0">
      <x-table.search route="{{ route('actions.index') }}"></x-table.search>
    </div>
  </div>
  <div class="collapse m-0 p-0 mb-3" id="filtros">
    <div class="card card-body m-0 p-3">
      <form action="{{ route('actions.index') }}" method="GET" class="row">
        @foreach ($parametros as $key => $parametro)
        <x-form-elements.select.select title="{{ ucfirst(strtolower($key)) }}" id="{{ strtolower($key) }}"
          name="{{ strtolower($key) }}" class="col-12 col-md-4 col-lg-3">

          <x-slot:options>
            <option value="" disabled {{ request(strtolower($key))=='' ? 'selected' : '' }}>Selecione</option>
            @foreach ($parametro as $lista)
            <option value="{{ $lista->value }}" {{ request(strtolower($key))==$lista->value ? 'selected' : '' }}>
              {{ $lista->value }}
            </option>
            @endforeach
          </x-slot:options>

        </x-form-elements.select.select>
        @endforeach

        <x-form-elements.select.select title="Status" id="status" name="status" class="col-12 col-md-4 col-lg-3">
          <x-slot:options>
            <option value="" disabled {{ request('status')===null ? 'selected' : '' }}>Selecione</option>
            <option value="00" {{ request('status')==='00' ? 'selected' : '' }}>Inativo</option>
            <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Ativo</option>
            <option value="2" {{ request('status')=='2' ? 'selected' : '' }}>Finalizado</option>
          </x-slot:options>
        </x-form-elements.select.select>

        @include('components.form-elements.input.input', [
        'title' => 'Ano',
        'type' => 'number',
        'class' => 'mb-3 col-12 col-md-4 col-lg-3',
        'name' => 'ano',
        'required' => 'false',
        'placeholder' => 'Ano',
        'value' => request('ano')
        ])

        <div class="col-12 col-md-4 col-lg-3">
          <label class="form-label d-block">&nbsp;</label>
          <div class="d-flex gap-2">
            <button class="btn btn-green w-100" type="submit">Filtrar</button>

            <a href="{{ route('actions.index') }}" class="btn btn-outline-secondary w-100">
              Limpar
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="table-responsive p-0" style="max-height: 60vh;">
    <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
      <thead>
        <tr style="position:sticky; top: 0; z-index: 1;">
          <th class="text-wrap" style="min-width: 400px;">titulo</th>
          <th style="">id_atividade</th>
          <th style="">id_projeto</th>
          <th style="">coordenador</th>
          <th style="">centro_departamento</th>
          <th style="">data_inicio</th>
          <th style="">data_fim</th>
          <th style="">ano</th>
          <th style="">tipo_acao</th>
          <th style="">area_tematica</th>
          <th style="">modalidade</th>
          <th style="">status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($actions as $item)
        <tr>
          <td class="text-wrap" style="min-width: 400px;">{{$item->titulo}}</td>
          <td>{{$item->id_atividade}}</td>
          <td>{{$item->id_projeto}}</td>
          <td>{{$item->coordenador->name}}</td>
          <td>{{$item->centro_departamento}}</td>
          <td>{{date('d-m-Y', strtotime($item->data_inicio))}}</td>
          <td>{{date('d-m-Y', strtotime($item->data_fim))}}</td>
          <td>{{$item->ano}}</td>
          <td>{{$item->tipo_acao}}</td>
          <td>{{$item->area_tematica}}</td>
          <td>{{$item->modalidade}}</td>
          <td>{{ $item->status == 0 ? 'Inativo' : ($item->status == 1 ? 'Ativo' : 'Finalizado') }}</td>
          <td>
            @can('editar_ação')
            <a href="">Editar</a>
          </td>
          @endcan
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-center mt-5">
    {{ $actions->links() }}
  </div>
</div>
@endsection
@section('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function() {
      const dropArea = document.getElementById("drop-area");
      const csvInput = document.getElementById("csv");
      const fileInfo = document.getElementById("file-info");

      const MAX_SIZE_MB = 10;

      function handleFile(file) {
        if (!file) return;

        // Verifica se é CSV
        if (!file.name.endsWith(".csv")) {
          fileInfo.textContent = "Por favor, selecione um arquivo CSV válido.";
          csvInput.value = "";
          return;
        }

        const sizeMB = file.size / (1024 * 1024);
        if (sizeMB > MAX_SIZE_MB) {
          fileInfo.textContent = "O arquivo ultrapassa 10MB.";
          csvInput.value = "";
          return;
        }

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        csvInput.files = dataTransfer.files;

        fileInfo.textContent = `Selecionado: ${file.name} (${sizeMB.toFixed(2)}MB)`;
      }

      dropArea.addEventListener("click", () => {
        csvInput.value = "";
        csvInput.click();
      });

      csvInput.addEventListener("change", () => {
        const file = csvInput.files[0];
        handleFile(file);
      });

      dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("dragover");
      });

      dropArea.addEventListener("dragleave", () => {
        dropArea.classList.remove("dragover");
      });

      dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("dragover");
        const file = e.dataTransfer.files[0];
        handleFile(file);
      });
    });
</script>
@endsection