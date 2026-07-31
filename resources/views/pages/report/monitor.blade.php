@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
    <div class="m-0 p-0 mb-4 row">
        <div class="btn-list col-12 col-md-6 p-0 m-0">
            <a href="{{route('report.index')}}" class="btn">Voltar página</a>
        </div>
        <div class="d-flex justify-content-end col-12 col-md-6 p-0 m-0">
            <x-table.search route="{{ route('report.monitor', $submissao->id) }}"></x-table.search>
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
                    'titulo' => 'Título',
                    'finalizada_em' => 'Data finalização'
                    ] as $field => $label)
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'dir' => $sortField === $field ? $nextDirection : 'asc']) }}"
                            class="text-reset text-decoration-none d-flex align-items-center gap-1"
                            style="{{ $label === 'Título' ? 'min-width: 200px;' : '' }}"
                            class="{{ $label === 'Título' ? 'text-wrap' : '' }}">
                            {{ $label }}
                            @if ($sortField === $field)
                            {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </a>
                    </th>
                    @endforeach
                    <th>Progresso do relatório</th>
                    <th>Progresso da avaliação</th>
                    <th width="5%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submissoes as $item)
                <tr>
                    <td class="text-wrap" style="min-width: 200px;">{{$item->acao->titulo}}</td>
                    <td>
                        {!! $item->finalizada_em == null
                        ? '<span class="badge bg-danger">Não finalizado</span>'
                        : '<span class="badge bg-success">' . date('d/m/Y H:i:s', strtotime($item->finalizada_em)) .
                            '</span>'
                        !!}
                    </td>
                    <td>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">{{$item->progress}}%</div>
                            <div class="col">
                                <div class="progress progress-sm">
                                    <div class="progress-bar" style="width: {{$item->progress}}%" role="progressbar"
                                        aria-valuenow="{{$item->progress}}" aria-valuemin="0" aria-valuemax="100"
                                        aria-label="{{$item->progress}} Completo">
                                        <span class="visually-hidden">{{$item->progress}}% Completo</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">{{$item->evaluationProgress}}%</div>
                            <div class="col">
                                <div class="progress progress-sm">
                                    <div class="progress-bar" style="width: {{$item->evaluationProgress}}%" role="progressbar"
                                        aria-valuenow="{{$item->evaluationProgress}}" aria-valuemin="0" aria-valuemax="100"
                                        aria-label="{{$item->evaluationProgress}} Completo">
                                        <span class="visually-hidden">{{$item->evaluationProgress}}% Completo</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="{{route('report.validator', $item)}}" class="btn btn-sm btn-success {{$item->finalizada_em == null ? 'disabled' : ''}}">Validar</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-5">
        {{ $submissoes->links() }}
    </div>
</div>
@endsection
@section('scripts')
@endsection