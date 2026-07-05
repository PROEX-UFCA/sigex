@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      @can('adicionar_ação')
      <a href="{{route('actions.create')}}" class="btn">Inserir ação</a>
      @endcan
      @can('importar_ações')
      <bottom class="btn" data-bs-toggle="modal" data-bs-target="#importar" aria-expanded="false"
        aria-controls="modalExample">Importar ações</bottom>
      <x-modal.modal id="importar" class="modal-center" title="Importar dados"
        route="{{route('actions.previewImport')}}" textBtnClose="Cancelar" textBtnSave="Importar"
        classBtnSave="btn-primary">
        <x-slot:content>
          <div class="card-body mb-3">
            <h3>Atenção para a Importação de Dados</h3>
            <p>
              Para garantir que a importação ocorra sem erros, seu arquivo <strong>.csv</strong> precisa conter as
              seguintes <strong>20 colunas</strong>, exatamente nesta ordem e com esta nomenclatura no cabeçalho:
            </p>

            <div class="mb-3">
              <span class="badge badge-dark mb-1">ID_PROJETO</span>
              <span class="badge badge-dark mb-1">ANO</span>
              <span class="badge badge-dark mb-1">TITULO</span>
              <span class="badge badge-dark mb-1">EDITAL</span>
              <span class="badge badge-dark mb-1">BOLSAS_SOLICITADAS</span>
              <span class="badge badge-dark mb-1">BOLSAS_CONCEDIDAS</span>
              <span class="badge badge-dark mb-1">FINANCIAMENTO_INTERNO</span>
              <span class="badge badge-dark mb-1">FINANCIAMENTO_EXTERNO</span>
              <span class="badge badge-dark mb-1">SITUACAO</span>
              <span class="badge badge-dark mb-1">DATA_CADASTRO</span>
              <span class="badge badge-dark mb-1">DATA_INICIO</span>
              <span class="badge badge-dark mb-1">DATA_FIM</span>
              <span class="badge badge-dark mb-1">DATA_ATUALIZACAO</span>
              <span class="badge badge-dark mb-1">SIGLA</span>
              <span class="badge badge-dark mb-1">TIPO_ACAO</span>
              <span class="badge badge-dark mb-1">AREA_TEMATICA</span>
              <span class="badge badge-dark mb-1">RESUMO</span>
              <span class="badge badge-dark mb-1">PALAVRAS_CHAVE</span>
              <span class="badge badge-dark mb-1">ODS</span>
              <span class="badge badge-dark mb-1">CONTEXTO</span>
            </div>

            <p><strong class="text-danger">Importante:</strong> O cabeçalho (primeira linha) do arquivo deve ter
              <strong>exatamente</strong> os nomes acima.
            </p>
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
      @can('importar_membros')
      <bottom class="btn" data-bs-toggle="modal" data-bs-target="#importar-membros" aria-expanded="false"
        aria-controls="modalExample">Importar membros</bottom>
      <x-modal.modal id="importar-membros" class="modal-center" title="Importar dados"
        route="{{route('membros.previewImport')}}" textBtnClose="Cancelar" textBtnSave="Importar"
        classBtnSave="btn-primary">
        <x-slot:content>
          <div class="card-body mb-3">
            <h3>Atenção para a Importação de Dados</h3>
            <p>
              Para garantir que a importação ocorra sem erros, seu arquivo <strong>.csv</strong> precisa conter as
              seguintes <strong>10 colunas</strong>, exatamente nesta ordem e com esta nomenclatura no cabeçalho:
            </p>

            <div class="mb-3">
              <span class="badge badge-dark mb-1">ID_PROJETO</span>
              <span class="badge badge-dark mb-1">ID_PESSOA</span>
              <span class="badge badge-dark mb-1">NOME</span>
              <span class="badge badge-dark mb-1">TIPO_MEMBRO</span>
              <span class="badge badge-dark mb-1">CATEGORIA_MEMBRO</span>
              <span class="badge badge-dark mb-1">E-MAIL</span>
              <span class="badge badge-dark mb-1">STATUS</span>
              <span class="badge badge-dark mb-1">DATA_INICIO</span>
              <span class="badge badge-dark mb-1">DATA_FIM</span>
              <span class="badge badge-dark mb-1">TIPO_VINCULO</span>
            </div>

            <p><strong class="text-danger">Importante:</strong> O cabeçalho (primeira linha) do arquivo deve ter
              <strong>exatamente</strong> os nomes acima.
            </p>
          </div>
          <div id="drop-area-2"
            class="rounded-4 d-flex flex-column justify-content-center align-items-center bg-light p-4 text-center"
            style="height: 150px; cursor: pointer; border: dashed 2px gray">
            <p class="text-muted mb-2">Arraste o .csv aqui ou clique para selecionar</p>
            <p class="text-red mb-2">Máximo 10MB</p>
            <input type="file" name="csv" id="csv-2" accept=".csv" required hidden>
            <div id="file-info-2" class="text-muted small mt-2"></div>
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
          'situacao' => 'situacao',
          'data_inicio' => 'Data de Início',
          'data_fim' => 'Data de Finalização',
          'tipo_acao' => 'Tipo de Ação',
          'modalidade_edital' => 'Modalidade/Edital',
          'area_tematica' => 'Área Temática',
          'centro_departamento_sigla' => 'Centro/Departamento/Sigla',
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
          <td>{{$item->tipo_acao}}</td>
          <td>{{$item->modalidade_edital}}</td>
          <td>{{$item->area_tematica}}</td>
          <td>{{$item->centro_departamento_sigla}}</td>
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
    
  document.addEventListener("DOMContentLoaded", function() {
      const dropArea = document.getElementById("drop-area-2");
      const csvInput = document.getElementById("csv-2");
      const fileInfo = document.getElementById("file-info-2");

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