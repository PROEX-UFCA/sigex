@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">

    @php
    $meses = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro',
    ];
    $ehPeriodoAtual = ($mesSelecionado === now()->month && $anoSelecionado === now()->year);
    @endphp

    <div class="card shadow-sm col-6 mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h3 class="card-title m-0 fw-bold">
                <i class="ti ti-calendar-stats me-2"></i>DADOS MENSAIS
            </h3>

            <form action="{{ route('vitrine.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                {{-- Preserva busca e ordenação da listagem caso estejam ativos --}}
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('dir'))
                    <input type="hidden" name="dir" value="{{ request('dir') }}">
                @endif

                <select name="mes" class="form-select w-auto" aria-label="Filtrar por mês" onchange="this.form.submit()">
                    @foreach($meses as $numero => $nomeMes)
                        <option value="{{ $numero }}" {{ $mesSelecionado === $numero ? 'selected' : '' }}>
                            {{ $nomeMes }}
                        </option>
                    @endforeach
                </select>

                <select name="ano" class="form-select w-auto" aria-label="Filtrar por ano" onchange="this.form.submit()">
                    @foreach($anosDisponiveis as $ano)
                        <option value="{{ $ano }}" {{ $anoSelecionado === $ano ? 'selected' : '' }}>
                            {{ $ano }}
                        </option>
                    @endforeach
                </select>

                @unless($ehPeriodoAtual)
                    <a href="{{ route('vitrine.index', request()->only(['search', 'sort', 'dir'])) }}"
                    class="btn btn-sm btn-ghost-secondary"
                    title="Voltar para o mês atual">
                        <i class="ti ti-rotate-clockwise"></i>
                    </a>
                @endunless
            </form>
        </div>

        <div class="list-group list-group-flush">
            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                <span><i class="ti ti-click text-muted me-2"></i> Acessos</span>
                <span class="badge bg-secondary text-white rounded-pill px-3">{{ $acessosGerais }}</span>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                <span><i class="ti ti-world text-muted me-2"></i> Visitantes</span>
                <span class="badge bg-blue text-white rounded-pill px-3">{{ $visitantesUnicos }}</span>
            </div>

            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                <span><i class="ti ti-building-community text-muted me-2"></i> Instituições externas conectadas</span>
                <span class="badge bg-success text-white rounded-pill px-3">{{ $instituicoesConectando }}</span>
            </div>
        </div>
    </div>

    <div class="card mb-3 row">
        <div class="row p-2">
            <div class="col-5 m-0 mt-3 text-uppercase">
                <h3>Listagem das Instituições Externas</h3>
            </div>
            <div class="d-flex justify-content-end col-7 p-0 m-0">
                <x-table.search route="{{ route('vitrine.index') }}"></x-table.search>
                <a href="{{ route('vitrine.index') }}" class="btn ms-2 my-2" title="Limpar busca">
                    Limpar
                </a>
            </div>
        </div>
        
        <div class="table-responsive p-0">
            <table class="table table-bordered align-middle mb-0 text-nowrap">
                <thead>
                    <tr>
                        @php
                        $sortField = request('sort', 'created_at');
                        $sortDirection = request('dir', 'desc');
                        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';
                        @endphp

                        @foreach ([
                        'nome' => 'nome',
                        'email' => 'email',
                        'cnpj' => 'cnpj',
                        'endereço' => 'endereço',
                        'status' => 'status',
                        'telefone_contato' => 'telefone',
                        'created_at' => 'Data',
                        ] as $field => $label)
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'dir' => $sortField === $field ? $nextDirection : 'asc']) }}"
                                class="text-reset text-decoration-none d-flex align-items-center gap-1"
                                style="{{ $label === 'nome' ? 'min-width: 200px;' : '' }}"
                                class="{{ $label === 'nome' ? 'text-wrap' : '' }}">
                                {{ $label }}
                                @if ($sortField === $field)
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                @endif
                            </a>
                        </th>
                        @endforeach
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($instituicoes as $item)
                    <tr>
                        <td class="text-wrap" style="min-width: 200px;">{{$item->nome}}</td>
                        <td>{{$item->email}}</td>
                        <td>{{$item->cnpj}}</td>
                        <td>{{$item->logradouro}}, {{$item->numero}}, {{$item->complemento ? $item->complemento .', ' : ''}} {{$item->cep}}</td>
                        <td class="text-wrap {{ $item->status == 0 ? 'bg-red-lt' : 'bg-success-lt' }}">{{ $item->status == 0 ?
                            'Inativo' : 'Ativo' }}</td>
                        <td>{{$item->telefone_contato}}</td>
                        <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
                        @if($item->status == 0)
                        <td class="text-center">
                            <form action="{{ route('vitrine.aprovar', $item->id) }}" method="POST"
                                onsubmit="return confirm('Deseja realmente aprovar esta instituição?')">
                                @csrf
                                <button
                                    class=" btn btn-sm btn-success px-2 btn-icon {{ $item->status == 0 ? '' : 'disabled' }}"
                                    type="submit" data-bs-toggle="tooltip" data-bs-placement="top" title="Aprovar?">
                                    Aprovar
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-5">
            {{ $instituicoes->links() }}
        </div>
    </div>
    @endsection
    @section('scripts')
    @endsection