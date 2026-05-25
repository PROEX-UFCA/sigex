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
            <a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'dir' => $sortField === $field ? $nextDirection : 'asc']) }}" class="text-reset text-decoration-none d-flex align-items-center gap-1">
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
          <td>{{ $item->status == 0 ? 'Inativo' : ($item->status == 1 ? 'Ativo' : 'Finalizado') }}</td>
          <td>{{date('d-m-Y', strtotime($item->data_inicio))}}</td>
          <td>{{date('d-m-Y', strtotime($item->data_fim))}}</td>
          <td>{{$item->coordenador->name}}</td>
          <td>{{$item->tipo_acao}}</td>
          <td>{{$item->modalidade}}</td>
          <td>{{$item->area_tematica}}</td>
          <td>{{$item->centro_departamento}}</td>
          {{-- <td>{{$item->id_atividade}}</td> --}}
          {{-- <td>{{$item->id_projeto}}</td> --}}
          @can('editar_ação')
          <td>
            <a href="" data-bs-toggle="modal"
              data-bs-target="#editarAcao"
              data-id="{{ $item->id }}"
              data-titulo="{{ $item->titulo }}"
              data-coordenador="{{ $item->coordenador->name }}"
              data-centro="{{ $item->centro_departamento }}"
              data-ano="{{ $item->ano }}"
              data-data-inicio="{{ $item->data_inicio }}"
              data-data-fim="{{ $item->data_fim }}"
              data-tipo="{{ $item->tipo_acao }}"
              data-area="{{ $item->area_tematica }}"
              data-modalidade="{{ $item->modalidade }}"
              data-status="{{ $item->status }}">
              Editar
            </a>
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
  @can('editar_ação')
  <x-modal.modal id="editarAcao" class="modal-center" title="Editar dados" route="#" textBtnClose="Cancelar" typeBtnClose="button" textBtnSave="Enviar" classBtnSave="btn-primary">
    <x-slot:content>
      <input type="hidden" name="_method" value="PATCH">
      <input type="hidden" name="id" id="edit-id">

      <div class="row g-2">
        @include('components.form-elements.input.input', [
          'title' => 'Título',
          'type' => 'text',
          'name' => 'titulo',
          'id' => 'edit-titulo',
          'required' => 'true',
          'value' => '',
          'class' => 'col-12'
        ])

        @include('components.form-elements.input.input', [
          'title' => 'Coordenador',
          'type' => 'text',
          'name' => 'coordenador',
          'id' => 'edit-coordenador',
          'required' => 'true',
          'value' => '',
          'class' => 'col-12 col-md-6'
        ])

        @include('components.form-elements.input.input', [
          'title' => 'Centro/Departamento',
          'type' => 'text',
          'name' => 'centro_departamento',
          'id' => 'edit-centro',
          'required' => 'true',
          'value' => '',
          'class' => 'col-12 col-md-6'
        ])

        @include('components.form-elements.input.input', [
          'title' => 'Ano',
          'type' => 'number',
          'name' => 'ano',
          'id' => 'edit-ano',
          'required' => 'true',
          'value' => '',
          'class' => 'col-12 col-md-4'
        ])

        @include('components.form-elements.input.input', [
          'title' => 'Data de Início',
          'type' => 'date',
          'name' => 'data_inicio',
          'id' => 'edit-data-inicio',
          'required' => 'true',
          'value' => '',
          'class' => 'col-12 col-md-4'
        ])

        @include('components.form-elements.input.input', [
          'title' => 'Data de Finalização',
          'type' => 'date',
          'name' => 'data_fim',
          'id' => 'edit-data-fim',
          'required' => 'true',
          'value' => '',
          'class' => 'col-12 col-md-4'
        ])

        <x-form-elements.select.select title="Tipo da Ação" id="edit-tipo" name="tipo_acao">
          <x-slot:options>
            <option value="Prestação de Serviços">Prestação de Serviços</option>
            <option value="Evento">Evento</option>
            <option value="Curso">Curso</option>
            <option value="Projeto">Projeto</option>
            <option value="Programa">Programa</option>
          </x-slot:options>
        </x-form-elements.select.select>

        <x-form-elements.select.select title="Área Temática" id="edit-area" name="area_tematica">
          <x-slot:options>
            <option value="Comunicação">Comunicação</option>
            <option value="Educação">Educação</option>
            <option value="Tecnologia e Produção">Tecnologia e Produção</option>
            <option value="Saúde">Saúde</option>
            <option value="Trabalho">Trabalho</option>
            <option value="Cultura">Cultura</option>
            <option value="Meio Ambiente">Meio Ambiente</option>
            <option value="Direitos Humanos e Justiça">Direitos Humanos e Justiça</option>
          </x-slot:options>
        </x-form-elements.select.select>

        <x-form-elements.select.select title="Modalidade" id="edit-modalidade" name="modalidade">
          <x-slot:options>
            <option value="Ampla Concorrência">Ampla Concorrência</option>
            <option value="Ação de Fluxo Contínuo">Ação de Fluxo Contínuo</option>
            <option value="Vinculada a Edital">Vinculada a Edital</option>
            <option value="UFCA Itinerante">UFCA Itinerante</option>
            <option value="PROPE">PROPE</option>
          </x-slot:options>
        </x-form-elements.select.select>

        <x-form-elements.select.select title="Status" id="edit-status" name="status">
          <x-slot:options>
            <option value="0">Inativo</option>
            <option value="1">Ativo</option>
            <option value="2">Finalizado</option>
          </x-slot:options>
        </x-form-elements.select.select>
      </div>
    </x-slot:content>
  </x-modal.modal>
  @endcan
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
  
  const editModal = document.getElementById('editarAcao');
  editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    document.getElementById('edit-id').value = button.dataset.id;
    document.getElementById('edit-titulo').value = button.dataset.titulo;
    document.getElementById('edit-coordenador').value = button.dataset.coordenador;
    document.getElementById('edit-centro').value = button.dataset.centro;
    document.getElementById('edit-ano').value = button.dataset.ano;
    document.getElementById('edit-data-inicio').value = button.dataset.dataInicio;
    document.getElementById('edit-data-fim').value = button.dataset.dataFim;
    document.getElementById('edit-tipo').value = button.dataset.tipo;
    document.getElementById('edit-area').value = button.dataset.area;
    document.getElementById('edit-modalidade').value = button.dataset.modalidade;
    document.getElementById('edit-status').value = button.dataset.status;

    const form = editModal.querySelector('form');
    form.action = `/acoes/editar/${button.dataset.id}`;
  })
</script>
@endsection