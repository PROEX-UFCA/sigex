@extends('templates.template')

@section('styles')
@endsection

@section('content')
<div class="page-body">
    {{-- Topo: Ações e Informações Gerais do Relatório --}}
    <div class="m-0 p-0 mb-4 row align-items-center">
        <div class="btn-list col-12 col-md-6 p-0 m-0">
            <a href="{{ route('report.index') }}" class="btn">Voltar página</a>
        </div>
        <div class="d-flex justify-content-end col-12 col-md-6 p-0 m-0">
            <x-table.search route="{{ route('report.edit', $relatorio->id) }}"></x-table.search>
        </div>
    </div>

    {{-- Caixa com as Especificações do Relatório --}}
    <div class="card mb-4 form-fieldset">
        <div class="card-header bg-light">
            <h3 class="card-title fw-bold">Parâmetros e Detalhes do Relatório</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <span class="text-muted d-block">Título do Relatório</span>
                    <strong class="fs-4">{{ $relatorio->titulo }}</strong>
                </div>
                <div class="col-12 col-md-3">
                    <span class="text-muted d-block">Formulário Vinculado</span>
                    <strong>{{ $relatorio->formulario->titulo ?? 'N/A' }}</strong>
                </div>
                <div class="col-6 col-md-2">
                    <span class="text-muted d-block">Data de Início</span>
                    <strong>{{ $relatorio->data_inicio ? date('d/m/Y H:i', strtotime($relatorio->data_inicio)) : '-'
                        }}</strong>
                </div>
                <div class="col-6 col-md-2">
                    <span class="text-muted d-block">Prazo Final</span>
                    <strong>{{ $relatorio->prazo ? date('d/m/Y H:i', strtotime($relatorio->prazo)) : '-' }}</strong>
                </div>
                <div class="col-12 col-md-1 text-md-end">
                    <span class="text-muted d-block">Total Vinculados</span>
                    <span class="badge bg-primary fs-3">{{ $submissoes->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabela de Submissões Existentes --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title fw-bold">Destinatários Atuais</h4>
        </div>
        <div class="table-responsive p-0">
            <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
                <thead>
                    <tr>
                        @php
                        $sortField = request('sort', 'created_at');
                        $sortDirection = request('dir', 'desc');
                        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';
                        @endphp

                        @foreach ([
                        'responsavel' => 'Responsável',
                        'titulo' => 'Título da Ação',
                        'finalizada_em' => 'Data finalização'
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
                        <th>Progresso do relatório</th>
                        <th>Progresso da avaliação</th>
                        <th width="5%" class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissoes as $item)
                    <tr>
                        <td>{{ $item->id_usuario == null ? "Não se aplica" : ($item->user->name ?? 'Usuário
                            #'.$item->id_usuario) }}</td>
                        <td class="text-wrap" style="min-width: 200px;">
                            {{ $item->acao != null ? $item->acao->titulo : "Não se aplica" }}
                        </td>
                        <td>
                            {!! $item->finalizada_em == null
                            ? '<span class="badge bg-danger">Não finalizado</span>'
                            : '<span class="badge bg-success">' . date('d/m/Y H:i:s', strtotime($item->finalizada_em)) .
                                '</span>'
                            !!}
                        </td>
                        <td>
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">{{ $item->progress }}%</div>
                                <div class="col">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar" style="width: {{ $item->progress }}%"
                                            role="progressbar" aria-valuenow="{{ $item->progress }}" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">{{ $item->evaluationProgress }}%</div>
                                <div class="col">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar" style="width: {{ $item->evaluationProgress }}%"
                                            role="progressbar" aria-valuenow="{{ $item->evaluationProgress }}"
                                            aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('submissao.delete', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tem certeza que deseja remover esta submissão?');">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhuma submissão cadastrada neste
                            relatório.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center my-3">
            {{ $submissoes->links() }}
        </div>
    </div>

    {{-- Seção: Filtrar e Adicionar Novos Destinatários --}}
    <div class="card form-fieldset mb-3">
        <div class="card-header bg-light">
            <h4 class="card-title fw-bold">Adicionar Novos Destinatários ao Relatório</h4>
        </div>
        <form class="card-body row p-3" method="POST" action="{{ route('report.edit', $relatorio->id) }}">
            @csrf
            <div class="col-12 row m-0 p-0">
                <x-form-elements.select.select title="Para que/quem deseja criar essa nova disponibilidade?" id="who"
                    name="who" class="col-12 col-md-4" required="true">
                    <x-slot:options>
                        <option value="" disabled {{ empty($who) ? 'selected' : '' }}>Selecione</option>
                        <option value="acoes" {{ $who=="acoes" ? 'selected' : '' }}>Para ações</option>
                        <option value="membros" {{ $who=="membros" ? 'selected' : '' }}>Para membros de ações</option>
                        <option value="usuarios" {{ $who=="usuarios" ? 'selected' : '' }}>Para usuários específicos
                        </option>
                    </x-slot:options>
                </x-form-elements.select.select>

                {{-- Filtros para Ações --}}
                <div class="row col-12 m-0 p-0 d-none" id="div-acoes">
                    <div>
                        <hr class="my-2">
                        <p class="fw-bold mb-3">Parâmetros / Filtros de Ações</p>
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
                            <label class="form-label required">Empresas Juniores?</label>
                            <label class="form-check">
                                <input class="form-check-input" type="radio" value="Sim" name="is_ej" {{
                                    request('is_ej')=='Sim' ? 'checked' : '' }}>
                                <span class="form-check-label">Sim</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="radio" value="0" name="is_ej" {{
                                    request('is_ej')=='0' ? 'checked' : '' }}>
                                <span class="form-check-label">Não</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Filtros para Membros --}}
                <div class="row col-12 m-0 p-0 d-none" id="div-membros">
                    <div>
                        <hr class="my-2">
                        <p class="fw-bold mb-3">Parâmetros / Filtros de Membros</p>
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

                <div class="col-12 d-flex justify-content-end mt-2">
                    <button type="submit" class="btn btn-primary px-4">
                        Filtrar Não Vinculados
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabela de Seleção para Inclusão de Novos Destinatários --}}
    @if(isset($items) && count($items) > 0)
    <form class="card form-fieldset mb-4" id="form-add-new" method="POST"
        action="{{ route('report.addSubmissions', $relatorio->id) }}">
        @csrf
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title fw-bold m-0">Resultados da Busca (Disponíveis: <span id="selected-count">{{
                    count($items) }}</span>)</h4>
            <input type="text" id="table-search" class="form-control form-control-sm w-auto"
                placeholder="Pesquisar resultados...">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-vcenter card-table table-striped" id="items-table">
                    <thead>
                        <tr>
                            <th class="w-1">
                                <input class="form-check-input" type="checkbox" id="select-all" checked>
                            </th>
                            <th>Nome / Descrição</th>
                            <th class="w-1 text-end">Remover</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                        @php
                        $idAcao = null;
                        $idUsuario = null;
                        $label = '';

                        if ($who === 'acoes') {
                        $idAcao = $item->id;
                        $coordenador = $item->coordenador();
                        $idUsuario = $coordenador->user->uuid ?? $coordenador->user_id ?? null;
                        $nomeCoord = $coordenador->user->name ?? $coordenador->nome ?? null;
                        $label = $item->titulo . ($nomeCoord ? ' (Coord: ' . $nomeCoord . ')' : '');
                        } elseif ($who === 'membros') {
                        $idAcao = $item->action->id ?? $item->id_acao ?? null;
                        $idUsuario = $item->user->uuid ?? $item->id_usuario ?? null;
                        $nomeUsuario = $item->user->name ?? $item->user->nome ?? 'Membro';
                        $tituloAcao = $item->action->titulo ?? 'Ação';
                        $label = $nomeUsuario . ' - ' . $tituloAcao;
                        } elseif ($who === 'usuarios') {
                        $idAcao = null;
                        $idUsuario = $item->uuid;
                        $label = $item->name ?? $item->nome ?? 'Usuário #'.$item->id;
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
                                <button type="button" class="btn btn-danger btn-icon btn-sm remove-row"
                                    title="Remover da seleção">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-success px-4">
                Vincular Destinatários Selecionados
            </button>
        </div>
    </form>
    @elseif(request()->isMethod('post'))
    <div class="alert alert-info text-center">
        Nenhum novo destinatário encontrado para os filtros selecionados ou todos já possuem este relatório vinculado.
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectWho = document.getElementById('who');
        const divAcoes = document.getElementById('div-acoes');
        const divMembros = document.getElementById('div-membros');

        function toggleWhoSections() {
            if (!selectWho) return;
            const value = selectWho.value;

            if (divAcoes) divAcoes.classList.add('d-none');
            if (divMembros) divMembros.classList.add('d-none');

            if (value === 'acoes' && divAcoes) {
                divAcoes.classList.remove('d-none');
            } else if (value === 'membros' && divMembros) {
                divMembros.classList.remove('d-none');
            }
        }

        if (selectWho) {
            selectWho.addEventListener('change', toggleWhoSections);
            toggleWhoSections();
        }

        // Pesquisa Instantânea na Tabela de Novos Resultados
        const searchInput = document.getElementById('table-search');
        if (searchInput) {
            searchInput.addEventListener('keyup', function () {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#items-table tbody tr');

                rows.forEach(row => {
                    const textCell = row.querySelector('.item-text');
                    if (textCell) {
                        const text = textCell.textContent.toLowerCase();
                        row.style.display = text.includes(filter) ? '' : 'none';
                    }
                });
            });
        }

        // Remoção dinâmica de linhas da seleção
        document.querySelectorAll('.remove-row').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                row.remove();
                updateCount();
            });
        });

        // Selecionar/Desselecionar todos
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateCount();
            });
        }

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