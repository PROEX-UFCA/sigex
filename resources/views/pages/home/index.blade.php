@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-header">
  <div class="">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">
          <a href="{{ route('home.index') }}">Home</a>
        </div>
        <h2 class="page-title">
          Home
        </h2>
      </div>
      <div class="col-auto ms-auto">
      </div>
    </div>
  </div>
</div>
<div class="page-body row">
  <div class="col-12 col-md-9">
    <h3 class="mb-0">Suas tarefas</h3>
    <hr class="my-3">
    <div class="card">
      <div class="card-header">
        Ralatórios para prencher
      </div>
      <div class="card-body">
        <div class="table-responsive p-0">
          <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
            <thead>
              <th>Título do relatório</th>
              <th>Data inicio</th>
              <th>Prazo</th>
              <th>Status</th>
              <th>Ação</th>
              <th>Preenchimento</th>
              <th>Avaliação</th>
              <th></th>
            </thead>
            <tbody>
              @foreach ($submissoes as $submissao)
              @if ((is_null($submissao->finalizada_em) || $submissao->evaluation_progress < 100 && $submissao->
                qtd_progress > 0) && $submissao->relatorio->status == 1 && $submissao->id_usuario ==
                auth()->user()->uuid)
                <tr>
                  <td>{{$submissao->relatorio->titulo}}</td>
                  <td>{{ date('d/m/Y H:i', strtotime($submissao->relatorio->data_inicio)) }}</td>
                  <td>{{ date('d/m/Y H:i', strtotime($submissao->relatorio->prazo)) }}</td>
                  <td>{{ $submissao->relatorio->status == 0 ? 'Inativo' : ($submissao->relatorio->status == 1 ? 'Ativo'
                    :
                    'Finalizado') }}</td>
                  <td>{{$submissao->action->titulo ?? 'Não está vinculada a nenhuma ação!'}}</td>
                  <td>
                    {{$submissao->progress}}%
                  </td>
                  <td>
                    {{$submissao->evaluation_progress}}%
                  </td>
                  <td><a href="{{ route('actions.report', $submissao->id) }}"
                      class="btn btn-sm btn-primary">Preencher</a></td>
                </tr>
                @endif
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-12 col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="subheader">Perfil Atualizado há</div>
        </div>
        <div class="h1 mb-3">{{ $user->updated_at->diffForHumans() }}</div>
        <div class="d-flex mb-2">
          <div>Nível de segurança do perfil</div>
          <div class="ms-auto">
            <span class="d-inline-flex align-items-center lh-1
                  {{ $user->updated_at->diffInMonths() < 2 ? 'text-green' : ($user->updated_at->diffInMonths() < 4 ? 'text-warning' : 'text-danger') }}
                ">
              {{ $progress }}
            </span>
          </div>
        </div>
        <div class="progress progress-sm">
          <div
            class="progress-bar {{ $user->updated_at->diffInMonths() < 2 ? 'bg-green' : ($user->updated_at->diffInMonths() < 4 ? 'bg-warning' : 'bg-danger') }}"
            style="width: {{ $progress }}" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <span class="visually-hidden">{{ $progress }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="card mt-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="h1 mb-0">Perfil {{ $profile < 100 ? 'Incompleto' : 'Completo' }}</div>
              <a class="btn btn-sm btn-primary rounded-2" id="sales-dropdown" href="{{ route('profile.index') }}">{{
                $profile < 100 ? 'Completar perfil' : 'Acessar perfil' }}</a>
          </div>
          <div class="d-flex mb-2">
            <div>Quantidade completa</div>
            <div class="ms-auto">
              <span class="d-inline-flex align-items-center lh-1
                  {{ $profile == 100 ? 'text-green' : ($profile > 60 ? 'text-warning' : 'text-danger') }}
                ">
                {{ $profile }}%
              </span>
            </div>
          </div>
          <div class="progress progress-sm">
            <div class="progress-bar {{ $profile == 100 ? 'bg-green' : ($profile > 60 ? 'bg-warning' : 'bg-danger') }}"
              style="width: {{ $profile }}%" aria-valuenow="{{ $profile }}" role="progressbar" aria-valuemin="0"
              aria-valuemax="100">
              <span class="visually-hidden">{{ $profile }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
@endsection