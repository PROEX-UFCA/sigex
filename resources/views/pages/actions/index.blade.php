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
      <x-modal.modal id="importar" class="modal-center" title="Importar dados" route="{{route('actions.import')}}"
        textBtnClose="Cancelar" textBtnSave="Importar" classBtnSave="btn-primary">
        <x-slot:content>
          <div class="card-body mb-3">
            <h3>Atenção para a Importação de Dados</h3>
            <p>
              Para garantir que a importação ocorra sem erros, seu arquivo <strong>.csv</strong> precisa conter as
              seguintes <strong>18 colunas</strong>, exatamente nesta ordem e com esta nomenclatura no cabeçalho:
            </p>

            <div class="mb-3">
              <span class="badge badge-dark mb-1">vazio com valores auto incrementado</span>
              <span class="badge badge-dark mb-1">ano</span>
              <span class="badge badge-dark mb-1">id_projeto</span>
              <span class="badge badge-dark mb-1">titulo</span>
              <span class="badge badge-dark mb-1">sigla</span>
              <span class="badge badge-dark mb-1">situacao</span>
              <span class="badge badge-dark mb-1">data_inicio</span>
              <span class="badge badge-dark mb-1">data_fim</span>
              <span class="badge badge-dark mb-1">data_atualizacao</span>
              <span class="badge badge-dark mb-1">resumo</span>
              <span class="badge badge-dark mb-1">palavras_chave</span>
              <span class="badge badge-dark mb-1">tipo_atividade</span>
              <span class="badge badge-dark mb-1">area_tematica</span>
              <span class="badge badge-dark mb-1">modalidade</span>
              <span class="badge badge-dark mb-1">com_bolsa</span>
              <span class="badge badge-dark mb-1">ods</span>
              <span class="badge badge-dark mb-1">proponente</span>
              <span class="badge badge-dark mb-1">email_proponente</span>
            </div>

            <p><strong class="text-danger">Importante:</strong> O cabeçalho (primeira linha) do arquivo deve ter
              <strong>exatamente</strong> os nomes acima.</p>
          </div>
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
          @php
          $sortField = request('sort', 'ano');
          $sortDirection = request('dir', 'desc');
          $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';
          @endphp

          @foreach ([
            'ano' => 'ano',
            'titulo' => 'Título',
            'status' => 'status',
            'data_inicio' => 'Data de Início',
            'data_fim' => 'Data de Finalização',
            'coordenador' => 'Coordenador',
            'tipo_acao' => 'Tipo de Ação',
            'modalidade' => 'Modalidade',
            'area_tematica' => 'Área Temática',
            'centro_departamento' => 'Centro/Departamento',
            // 'id_atividade' => 'id_atividade',
            // 'id_projeto' => 'id_projeto',
          ] as $field => $label)
          <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'dir' => $sortField === $field ? $nextDirection : 'asc']) }}"
              class="text-reset text-decoration-none d-flex align-items-center gap-1">
              {{ $label }}
              @if ($sortField === $field)
              {{ $sortDirection === 'asc' ? '↑' : '↓' }}
              @endif
            </a>
          </th>

          @endforeach
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($actions as $item)
        <tr>
          <td>{{$item->ano}}</td>
          <td class="text-wrap" style="min-width: 400px;">{{$item->titulo}}</td>
          <td>{{ $item->situacao }}</td>
          <td>{{date('d-m-Y', strtotime($item->data_inicio))}}</td>
          <td>{{date('d-m-Y', strtotime($item->data_fim))}}</td>
          <td>{{$item->coordenador->name}}</td>
          <td>{{$item->tipo_acao}}</td>
          <td>{{$item->modalidade}}</td>
          <td>{{$item->area_tematica}}</td>
          <td>{{$item->centro_departamento}}</td>
          @can('editar_ação')
          <td>
            @can('editar_ação')
            <a href="{{route('actions.edit', $item->id)}}">
              Editar
            </a>
            @endcan
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