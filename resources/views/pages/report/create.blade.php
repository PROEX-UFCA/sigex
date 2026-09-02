@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      <a href="{{route('report.index')}}" class="btn">Voltar página</a>
    </div>
  </div>
  <div class="card form-fieldset mb-3">
    <form class="card-body row p-0" method="POST" action="{{ route('report.create') }}" id="form-filtro">
      @csrf
      <div class="col-12 row m-0 p-0">

        <x-form-elements.select.select title="Para que/quem deseja criar esse relatório?" id="who" name="who"
          class="col-12 col-md-4" required="true">
          <x-slot:options>
            <option value="" disabled {{ empty($who) ? 'selected' : '' }}>Selecione</option>
            <option value="acoes" {{ $who=="acoes" ? 'selected' : '' }}>Para ações</option>
            <option value="membros" {{ $who=="membros" ? 'selected' : '' }}>Para membros de ações</option>
            <option value="usuarios" {{ $who=="usuarios" ? 'selected' : '' }}>Para usuários específicos</option>
          </x-slot:options>
        </x-form-elements.select.select>

        {{-- Filtros de Ações --}}
        <div class="row col-12 m-0 p-0 d-none" id="div-acoes">
          <div>
            <hr class="my-2">
            <p class="fw-bold mb-3">Parâmetros/Filtros de disponibilização</p>
          </div>
          @foreach ($parametros as $key => $parametro)
          <div class="col-12 col-md-3 mb-3">
            <div class="card p-3 h-100">
              <label class="form-label required">{{ ucfirst(strtolower($key)) }}</label>
              @foreach ($parametro as $item)
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $item->value }}"
                  name="parametros[{{strtolower($key)}}][]" {{ in_array($item->value,
                request("parametros.".strtolower($key), [])) ? 'checked' : '' }}>
                <span class="form-check-label">{{ $item->value }}</span>
              </label>
              @endforeach
            </div>
          </div>
          @endforeach
          <div class="col-12 col-md-3 mb-3">
            <div class="card p-3 h-100">
              <label class="form-label required">Empresas Júniores?</label>
              <label class="form-check">
                <input class="form-check-input" type="radio" value="1" name="is_ej" {{ request('is_ej')=='1' ? 'checked'
                  : '' }}>
                <span class="form-check-label">Sim</span>
              </label>
              <label class="form-check">
                <input class="form-check-input" type="radio" value="0" name="is_ej" {{ request('is_ej')=='0' ? 'checked'
                  : '' }}>
                <span class="form-check-label">Não</span>
              </label>
            </div>
          </div>
        </div>

        {{-- Filtros de Membros --}}
        <div class="row col-12 m-0 p-0 d-none" id="div-membros">
          <div>
            <hr class="my-2">
            <p class="fw-bold mb-3">Parâmetros/Filtros de disponibilização</p>
          </div>
          @foreach ($parametros_membros as $key => $parametro)
          <div class="col-12 col-md-4 mb-3">
            <div class="card p-3 h-100">
              <label class="form-label required">{{ ucfirst(strtolower($key)) }}</label>
              @foreach ($parametro as $item)
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $item->value }}"
                  name="parametros[{{strtolower($key)}}][]" {{ in_array($item->value,
                request("parametros.".strtolower($key), [])) ? 'checked' : '' }}>
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
          <button type="submit" class="btn btn-primary px-4" form="form-filtro">
            Filtrar
          </button>
        </div>

      </div>
    </form>
  </div>

  {{-- Exibido apenas quando houver dados filtrados --}}
  <form class="row m-0 p-0 align-items-stretch {{ (isset($items) && count($items) > 0) ? '' : 'd-none' }}"
    id="form-create" method="POST" action="{{ route('report.store') }}">
    @csrf
    <input type="hidden" name="who" id="who2" value="{{ $who }}">

    {{-- Lado Esquerdo: Form de Cadastro --}}
    <div class="col-12 col-md-4 ps-0 mb-3 mb-md-0">
      <div class="card card-body form-fieldset h-100">
        <div class="row m-0 p-0">
          @include('components.form-elements.input.input', [
          'title' => 'Título do relatório',
          'type' => 'text',
          'class' => 'mb-3 col-12',
          'name' => 'titulo',
          'required' => 'true',
          'placeholder' => 'Digite o nome do relatório.',
          'value' => old('titulo') ?? '',
          ])

          @include('components.form-elements.input.input', [
          'title' => 'Data de início',
          'type' => 'datetime-local',
          'class' => 'mb-3 col-12 col-lg-6',
          'name' => 'data_inicio',
          'required' => 'true',
          'value' => old('data_inicio') ?? '',
          ])

          @include('components.form-elements.input.input', [
          'title' => 'Prazo',
          'type' => 'datetime-local',
          'class' => 'mb-3 col-12 col-lg-6',
          'name' => 'prazo',
          'required' => 'true',
          'value' => old('prazo') ?? '',
          ])

          <x-form-elements.select.select title="Formulário" id="formulario" name="formulario" class="col-12 mb-3"
            required="true">
            <x-slot:options>
              <option value="" disabled {{ request('formulario')===null ? 'selected' : '' }}>Selecione</option>
              @foreach ($formularios as $formulario)
              <option value="{{ $formulario->id }}" {{ old('formulario')==$formulario->id ? 'selected' : '' }}>
                {{ $formulario->titulo }}
              </option>
              @endforeach
            </x-slot:options>
          </x-form-elements.select.select>

          <div class="col-12 mt-auto d-flex justify-content-end">
            <button type="submit" class="btn btn-success w-100" form="form-create">
              Salvar e Gerar Relatórios
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Lado Direito: Tabela Interativa com Filtro Instantâneo --}}
    <div class="col-12 col-md-8 pe-0">
      <div class="card card-body form-fieldset h-100 d-flex flex-column">

        <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
          <span class="fw-bold">Itens Selecionados (<span id="selected-count">{{ count($items ?? []) }}</span>)</span>
          <input type="text" id="table-search" class="form-control form-control-sm w-auto"
            placeholder="Pesquisar na tabela...">
        </div>

        <div class="table-responsive flex-grow-1" style="max-height: 400px; overflow-y: auto;">
          <table class="table table-vcenter card-table table-striped" id="items-table">
            <thead>
              <tr>
                <th class="w-1">
                  <input class="form-check-input" type="checkbox" id="select-all" checked>
                </th>
                <th>Nome / Descrição</th>
                <th class="w-1 text-end">Ação</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($items as $item)
              @php
              $idAcao = null;
              $idUsuario = null;
              $label = '';

              if ($who === 'acoes') {
              $idAcao = $item->id;
              $coordenador = $item->coordenador();
              $idUsuario = $coordenador->user->uuid ?? null;

              $nomeCoord = $coordenador->user->name ?? $coordenador->nome ?? null;
              $label = $item->titulo . ($nomeCoord ? ' (Coord: ' . $nomeCoord . ')' : '');
              } elseif ($who === 'membros') {
              $idAcao = $item->action->id ?? $item->id_acao ?? null;
              $idUsuario = $item->user->uuid ?? null;

              $nomeUsuario = $item->user->name ?? $item->user->nome ?? 'Membro';
              $tituloAcao = $item->action->titulo ?? 'Ação';
              $label = $nomeUsuario . ' - ' . $tituloAcao;
              } elseif ($who === 'usuarios') {
              $idAcao = null;
              $idUsuario = $item->uuid;
              $label = $item->name ?? $item->nome ?? 'Usuário #'.$item->uuid;
              }

              $targetData = json_encode([
              'id_acao' => $idAcao,
              'id_usuario' => $idUsuario
              ]);
              @endphp

              <tr>
                <td>
                  <input class="form-check-input item-checkbox" type="checkbox" name="target_ids[]"
                    value="{{ $targetData }}" checked>
                </td>
                <td class="item-text">
                  {{ $label }}
                </td>
                <td class="text-end">
                  <button type="button" class="btn btn-danger btn-icon btn-sm remove-row" title="Remover da lista">
                    <i class="ti ti-trash"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr id="empty-row">
                <td colspan="3" class="text-center text-muted py-4">Nenhum dado encontrado para os filtros selecionados.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </form>
</div>
@endsection
@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const selectWho = document.getElementById('who');
    const divAcoes = document.getElementById('div-acoes');
    const divMembros = document.getElementById('div-membros');

    // Alterna a exibição das seções de filtros conforme a seleção do select "who"
    function toggleWhoSections() {
        const value = selectWho.value;

        if (divAcoes) divAcoes.classList.add('d-none');
        if (divMembros) divMembros.classList.add('d-none');

        if (value === 'acoes' && divAcoes) {
            divAcoes.classList.remove('d-none');
        } else if (value === 'membros' && divMembros) {
            divMembros.classList.remove('d-none');
        }
    }

    selectWho.addEventListener('change', toggleWhoSections);
    toggleWhoSections();

    // Pesquisa Instantânea na Tabela
    const searchInput = document.getElementById('table-search');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#items-table tbody tr:not(#empty-row)');

            rows.forEach(row => {
                const text = row.querySelector('.item-text').textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // Remover Linha da Tabela
    document.querySelectorAll('.remove-row').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            row.remove();
            updateCount();
        });
    });

    // Selecionar / Desselecionar todos
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateCount();
        });
    }

    // Atualiza contador de itens ativos
    function updateCount() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        const countSpan = document.getElementById('selected-count');
        if (countSpan) countSpan.textContent = checkedCount;
    }

    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.addEventListener('change', updateCount);
    });
  });
</script>
@endsection