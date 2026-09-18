@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">

    <div class="row row-cards mb-4 m-0 p-0">
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-blue text-white avatar">
                                <i class="ti ti-building-community fs-2"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium fs-3">Total Registradas</div>
                            <div class="text-muted">{{ $totalInstituicoes }} instituições na base</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
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

    <div class="m-0 p-0 mb-4 row">
        {{-- <div class="btn-list col-12 col-md-6 p-0 m-0"> --}}
            <div class="d-flex justify-content-end col-12 p-0 m-0">
                <x-table.search route="{{ route('vitrine.index') }}"></x-table.search>
            </div>
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
                        <td class="{{ $item->status == 0 ? 'bg-red-lt' : 'bg-success-lt' }}">{{ $item->status == 0 ?
                            'Inativo' : 'Ativo' }}</td>
                        <td>{{$item->telefone_contato}}</td>
                        <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
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