@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="row">
    <div class="col-12 col-md-2">
      <div class="card">
        <div class="card-body">
          <div>
            <small class="text-muted fw-bold text-uppercase d-block mb-2">Ferramentas</small>
            @can('ver_formulários')
            <a href="{{ route('forms.index') }}" class="btn btn-outline-dark w-100 btn-sm rounded-2">Formulários</a>
            @endcan
          </div>
          <hr class="my-3">
          <div class="mb-3">
            <small class="text-muted fw-bold text-uppercase d-block mb-2">Área de relatórios</small>
            @can('ver_relatórios')
            <a href="{{route('report.index')}}" class="btn btn-sm rounded-2 btn-primary w-100">Acessar relatórios</a>
            @endcan
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-10 card p-3">
      <div class="m-0 p-0 row mb-4">
        <div class="col-12 btn-list col-md-6 p-0 m-0 d-flex align-items-center justify-content-start">
          <span class="badge bg-dark-lt fs-5 p-2">Todas as ações</span>
        </div>
        <div class="d-flex justify-content-end col-12  col-md-6 p-0 m-0">
          <bottom class="btn me-1" data-bs-toggle="collapse" data-bs-target="#filtros" aria-expanded="false"
            aria-controls="collapseExample">Filtros</bottom>
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
                <a href="{{route('actions.edit', $item->id)}}" class="btn btn-sm btn-primary btn-icon"
                  data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
                  <i class="ti ti-pencil"></i>
                </a>
                @endcan
              </td>
              @endcan
              @can('detalhar_ação')
              <td>
                <a href="{{ route('actions.details', $item->id) }}" class="btn btn-sm btn-info btn-icon"
                  data-bs-toggle="tooltip" data-bs-placement="top" title="Detalhar">
                  <i class="ti ti-eye"></i>
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
    </div>
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const form1 = document.getElementById('modal-formimportar');
      const form2 = document.getElementById('modal-formimportar-membros');
      const overlay = document.getElementById('loading-overlay');

      form1.addEventListener('submit', function() {
        overlay.style.display = 'flex';
      });
      form2.addEventListener('submit', function() {
        overlay.style.display = 'flex';
      });
    });
</script>
@endsection