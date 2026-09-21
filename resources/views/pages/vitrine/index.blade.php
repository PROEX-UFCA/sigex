@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">

    <div class="row mb-3">
        <div class="card col-6">
            <h3 class="card-title text-brown fw-bold my-3 ms-3 text-uppercase"><i class="ti ti-calendar-stats"></i> Dados mensais</h3>
            <div class="shadow-sm border-secondary border-top border-1">
                <div class="fs-4">
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom border-1">
                        <span><i class="ti ti-click text-muted me-2"></i> Acessos</span>
                        <span class="badge bg-secondary text-white rounded-pill fs-5 px-3 py-1">{{ $acessosGerais }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-1">
                        <span><i class="ti ti-world text-muted me-2"></i> Visitantes</span>
                        <span class="badge bg-blue text-white rounded-pill fs-5 px-3 py-1">{{ $visitantesUnicos }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <span><i class="ti ti-building-community text-muted me-2"></i> Instituições externas conectadas</span>
                        <span class="badge bg-success text-white rounded-pill fs-5 px-3 py-1">{{ $instituicoesConectando }}</span>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="ms-2 row row-cards p-0">
                <div class="card col-12">
                    <div class="card card-sm shadow-sm border-0">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-blue text-white avatar">
                                        <i class="ti ti-building-community fs-2"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium fs-3">Total Ativas</div>
                                    <div class="text-muted">{{ $totalInstituicoes }} {{ $totalInstituicoes == 1 ? 'instituição' : 'instituições' }} na base</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-sm shadow-sm border-0">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-yellow text-white avatar">
                                        <i class="ti ti-clock-exclamation fs-2"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium fs-3">Aguardando Aprovação</div>
                                    <div class="text-muted">{{ $pendentesInstituicoes }} {{ $pendentesInstituicoes == 1 ? 'pendente' : 'pendentes' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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