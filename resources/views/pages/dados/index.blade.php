@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-header">
    <div class="">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="col">
                    <div class="page-pretitle">
                        <a href="{{ route('logs.index') }}">Gerenciamento de dados</a>
                    </div>
                    <h2 class="page-title">
                        Sincronização dos dados
                    </h2>
                </div>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <form method="POST" action="{{ route('dados.sincronizar') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary" id="btnSincronizar">
                            <i class="fas fa-sync-alt"></i>
                            Sincronizar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body row">

</div>
@endsection
@section('scripts')
@endsection