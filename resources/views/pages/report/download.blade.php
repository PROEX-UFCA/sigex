@extends('templates.template')

@section('styles')
@endsection

@section('content')
<div class="page-body row">
  <div class="m-0 p-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      <a href="{{ route('report.index') }}" class="btn">Voltar página</a>
    </div>
  </div>

  <div class="card form-fieldset mb-3">
    <form class="card-body row p-0" method="POST" action="{{ route('report.baixar') }}" id="form-filtro">
      @csrf
      <div class="col-12 row m-0 p-0">

        {{-- Seleção do Objeto de Download --}}
        <x-form-elements.select.select title="O que deseja baixar?" id="what" name="what" class="col-12 col-md-8" required="true">
          <x-slot:options>
            <option value="" disabled selected>Selecione</option>
            <option value="relatorios">Relatórios (Dados relacionados com os relatórios)</option>
            <option value="acoes">Ações (Dados relacionados com ações)</option>
            <option value="membros">Membros (Dados relacionados apenas com membros de ações)</option>
            <option value="instituicoes">Instituições (Informações de instituições)</option>
          </x-slot:options>
        </x-form-elements.select.select>

        {{-- Seleção do Formato de Saída --}}
        <x-form-elements.select.select title="Formato" id="format" name="format" class="col-12 col-md-4" required="true">
          <x-slot:options>
            <option value="" disabled selected>Selecione</option>
            <option value="pdf">PDF</option>
            <option value="excel">Excel</option>
            <option value="csv">CSV</option>
          </x-slot:options>
        </x-form-elements.select.select>

        {{-- Filtros de Relatórios --}}
        <div class="row col-12 m-0 p-0 d-none" id="div-relatorios">
          <div>
            <hr class="my-2">
            <p class="fw-bold mb-3">Parâmetros / Filtros de Relatórios</p>
          </div>

          <x-form-elements.select.select title="Selecione um relatório" id="what_report" name="what_report" class="col-12 col-md-6" required="true">
            <x-slot:options>
              <option value="" selected disabled>Selecione</option>
              @foreach ($reports as $report)
                <option value="{{ $report->id }}">{{ $report->titulo }}</option>
              @endforeach
            </x-slot:options>
          </x-form-elements.select.select>

          <x-form-elements.select.select title="Quais submissões?" id="quais" name="quais" class="col-12 col-md-6" required="true">
            <x-slot:options>
              <option value="todos" selected>Todos</option>
              <option value="finalizados">Finalizados</option>
              <option value="nao_finalizados">Não finalizados</option>
            </x-slot:options>
          </x-form-elements.select.select>
        </div>

        {{-- Filtros de Ações --}}
        <div class="row col-12 m-0 p-0 d-none" id="div-acoes">
          <div>
            <hr class="my-2">
            <p class="fw-bold mb-3">Parâmetros / Filtros de Ações</p>
          </div>

          @foreach ($parametros_acao as $key => $parametro)
          <div class="col-12 col-md-3 mb-3">
            <div class="card p-3 h-100">
              <label class="form-label">{{ ucfirst(strtolower($key)) }}</label>
              @foreach ($parametro as $item)
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $item->value }}"
                  name="parametros[{{ strtolower($key) }}][]"
                  {{ in_array($item->value, request("parametros.".strtolower($key), [])) ? 'checked' : '' }}>
                <span class="form-check-label">{{ $item->value }}</span>
              </label>
              @endforeach
            </div>
          </div>
          @endforeach

          <div class="col-12 col-md-3 mb-3">
            <div class="card p-3 h-100">
              <label class="form-label">Anos</label>
              @foreach ($anos as $key => $item)
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $key }}"
                  name="anos[]" {{ in_array($key, request("anos", [])) ? 'checked' : '' }}>
                <span class="form-check-label">{{ $key }}</span>
              </label>
              @endforeach
            </div>
          </div>

          <div class="col-12 col-md-3 mb-3">
            <div class="card p-3 h-100">
              <label class="form-label">Empresas Juniores?</label>
              <label class="form-check">
                <input class="form-check-input" type="radio" value="1" name="is_ej" {{ request('is_ej')=='1' ? 'checked' : '' }}>
                <span class="form-check-label">Sim</span>
              </label>
              <label class="form-check">
                <input class="form-check-input" type="radio" value="0" name="is_ej" {{ request('is_ej')=='0' ? 'checked' : '' }}>
                <span class="form-check-label">Não</span>
              </label>
              <label class="form-check">
                <input class="form-check-input" type="radio" value="todas" name="is_ej" {{ request('is_ej', 'todas')=='todas' ? 'checked' : '' }}>
                <span class="form-check-label">Todas</span>
              </label>
            </div>
          </div>
        </div>

        {{-- Filtros de Membros --}}
        <div class="row col-12 m-0 p-0 d-none" id="div-membros">
          <div>
            <hr class="my-2">
            <p class="fw-bold mb-3">Parâmetros / Filtros de Membros</p>
          </div>

          @foreach ($parametros_membros as $key => $parametro)
          <div class="col-12 col-md-4 mb-3">
            <div class="card p-3 h-100">
              <label class="form-label">{{ ucfirst(strtolower($key)) }}</label>
              @foreach ($parametro as $item)
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $item->value }}"
                  name="parametros[{{ strtolower($key) }}][]"
                  {{ in_array($item->value, request("parametros.".strtolower($key), [])) ? 'checked' : '' }}>
                <span class="form-check-label">{{ $item->value }}</span>
              </label>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>

        <div class="col-12">
          <hr class="my-2">
        </div>

        <div class="col-12 d-flex justify-content-end mt-2">
          <button type="submit" class="btn btn-success px-4" form="form-filtro">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
              <polyline points="7 11 12 16 17 11" />
              <line x1="12" y1="4" x2="12" y2="16" />
            </svg>
            Baixar
          </button>
        </div>

      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const selectWhat = document.getElementById('what');
    const divRelatorios = document.getElementById('div-relatorios');
    const divAcoes = document.getElementById('div-acoes');
    const divMembros = document.getElementById('div-membros');

    function toggleWhoSections() {
        const value = selectWhat.value;

        // Oculta e desabilita os inputs das seções inativas
        [divRelatorios, divAcoes, divMembros].forEach(div => {
            if (div) {
                div.classList.add('d-none');
                div.querySelectorAll('input, select').forEach(el => el.disabled = true);
            }
        });

        let targetDiv = null;
        if (value === 'relatorios') targetDiv = divRelatorios;
        else if (value === 'acoes') targetDiv = divAcoes;
        else if (value === 'membros') targetDiv = divMembros;

        // Exibe e habilita os inputs da seção ativa
        if (targetDiv) {
            targetDiv.classList.remove('d-none');
            targetDiv.querySelectorAll('input, select').forEach(el => el.disabled = false);
        }
    }

    selectWhat.addEventListener('change', toggleWhoSections);
    toggleWhoSections();
  });
</script>
@endsection