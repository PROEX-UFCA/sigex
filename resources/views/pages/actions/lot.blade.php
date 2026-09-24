@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
    <div class="m-0 p-0 mb-4 row">
        <div class="btn-list col-12 p-0 m-0 mb-3">
            <div class="d-flex justify-content-between col-12 p-0 m-0">
                <a href="{{route('actions.index')}}" class="btn">Voltar página</a>
                @if ($actions->count() > 0)
                <form action="{{ route('actions.editLotStore') }}" method="POST"
                    onsubmit="return confirm('Deseja realmente concluir as ações e os membros ativos? O processo é irreversível.')"
                    style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        Executar
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <p class="text-warning mt-3 border-start border-3 border-warning ps-3">
                Essa página é destinada para finalizar as ações em execução e membros que estão ativos para finalizados,
                tome cuidado ao fazer, pois esse processo é irreversível!
            </p>
        </div>

        <div class="table-responsive p-2 card" style="max-height: 60vh;">
            @if ($actions->count() == 0)
            Não há registros.
            @else
            <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
                <thead>
                    <tr style="position:sticky; top: 0; z-index: 1;">
                        <th>ano</th>
                        <th>titulo</th>
                        <th>situacao</th>
                        <th>data_inicio</th>
                        <th>data_fim</th>
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
        <div class="d-flex justify-content-center mt-5">
            {{ $actions->links() }}
        </div>
    </div>
    @endsection
    @section('scripts')
    @endsection