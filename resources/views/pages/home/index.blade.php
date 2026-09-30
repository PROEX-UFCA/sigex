@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-header">
  <div class="">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">
          <a href="{{ route('home.index') }}">Início</a>
        </div>
        <h2 class="page-title">
          Início
        </h2>
      </div>
      <div class="col-auto ms-auto">
      </div>
    </div>
  </div>
</div>

<div class="page-body row">

  <div class="col-12 col-md-9">
    @if(isset($pendingMatches) && $pendingMatches->count() > 0)
        <h3 class="mb-0 text-primary">
            <i class="ti ti-bell-ringing me-2"></i>Novos Interesses
        </h3>
        <hr class="my-3">
        @foreach($pendingMatches as $match)
            <div class="card mb-3 shadow-sm border-start">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center my-1 ms-3">
                        <div>
                            <h3 class="m-0 text-brown">Nova demonstração de interesse!</h3>
                            <div class="text-muted fs-3">A instituição <strong>{{ $match->instituicao->nome }}</strong> deseja firmar uma parceria com o projeto <strong>{{ $match->acao->titulo }}</strong>.</div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded mb-1 text-muted border border-light">
                        <div class="row row-cols-1 row-cols-md-2 g-3 mt-1">
                            <div class="col">
                                <strong><i class="ti ti-mail me-1"></i> Email:</strong> <br> 
                                <a href="mailto:{{ $match->instituicao->email }}" class="text-decoration-none text-brown">{{ $match->instituicao->email }}</a>
                            </div>
                            <div class="col">
                                <strong><i class="ti ti-phone me-1"></i> Telefone:</strong> <br> 
                                {{ $match->instituicao->telefone_contato }}
                            </div>
                            <div class="col-12">
                                <strong><i class="ti ti-map-pin me-1"></i> Endereço:</strong> <br> 
                                {{ $match->instituicao->logradouro }}, {{ $match->instituicao->numero }} {{ $match->instituicao->complemento ? ' - ' . $match->instituicao->complemento : '' }} - CEP: {{ $match->instituicao->cep }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-1">
                        <form action="{{ route('actions.match.confirm', $match->id) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-success fw-bold shadow-sm px-4">
                                <i class="ti ti-check me-2"></i> Aceitar Parceria
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
    
    <h3 class="mb-0">Suas tarefas</h3>
    <hr class="my-3">
    <div class="card">
      <div class="card-header">
        Relatórios para prencher
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