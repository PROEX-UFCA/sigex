@extends('pages.vitrine.template')

@section('content')
<div class="page-header">
  <div class="">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="text-uppercase">
          <a href="{{ route('profile.index') }}" class="text-yellow fw-bold">Perfil da Instituição</a>
        </div>
        <h2 class="page-title">Informações</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body row">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-2 badge bg-brown w-auto mb-3 p-2 d-inline-flex">
            <i class="ti ti-user icon fs-2 m-0 p-0"></i>
            <h3 class="text-capitalize fs-2 m-0 p-0">{{ $instituicao->nome }}</h3>
        </div>

        <div class="mb-4 d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-yellow fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-parcerias">
                <i class="ti ti-list-details me-2"></i> Detalhar suas parcerias
            </button>
            
            <button type="button" class="btn btn-outline-brown fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-edit-profile">
                <i class="ti ti-edit me-2"></i> Editar Perfil
            </button>
        </div>

        <div class="text-muted row">
            <div class="col">
                <h4 class="border-bottom border-brown pb-2">Dados gerais</h4>
                <p><strong>CNPJ:</strong> {{ $instituicao->cnpj }}</p>
                <p><strong>Email:</strong> {{ $instituicao->email }}</p>
                <p><strong>Telefone:</strong> {{ $instituicao->telefone_contato }}</p>
            </div>
            <div class="col">
                <h4 class="border-bottom border-brown pb-2">Endereço</h4>
                <p>{{ $instituicao->logradouro }}, {{ $instituicao->numero }}</p>
                <p>{{ $instituicao->complemento }}</p>
                <p><strong>CEP:</strong> {{ $instituicao->cep }}</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-parcerias" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fs-3 text-brown"><i class="ti ti-heart-handshake me-2"></i> Histórico de Parcerias</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-ativas" class="nav-link active fw-bold" data-bs-toggle="tab">Ativas e Pendentes</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-finalizadas" class="nav-link fw-bold" data-bs-toggle="tab">Finalizadas</a>
                    </li>
                </ul>
                <div class="tab-content p-4">
                    <!-- Tab 1: Active & Pending -->
                    <div class="tab-pane active show" id="tabs-ativas">
                        @forelse($matches->where('concluida', false) as $match)
                            @if($match->mutual == true)
                                <div class="alert alert-success shadow-sm mb-3 border-0">
                                    <i class="ti ti-check me-2"></i>
                                    <strong>Conexão firmada!</strong> A coordenação de <strong>{{ $match->acao->titulo }}</strong> aceitou sua parceria.
                                </div>
                            @else
                                <div class="alert alert-info shadow-sm mb-3 border-0 opacity-75">
                                    <i class="ti ti-clock me-2"></i>
                                    <strong>Aguardando resposta.</strong> O interesse em <strong>{{ $match->acao->titulo }}</strong> foi enviado à coordenação.
                                </div>
                            @endif
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="ti ti-ghost fs-1 mb-2"></i>
                                <p>Nenhuma parceria ativa ou pendente no momento.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Tab 2: Finished -->
                    <div class="tab-pane" id="tabs-finalizadas">
                        @forelse($matches->where('concluida', true) as $match)
                            <div class="alert alert-secondary shadow-sm mb-3 border-0">
                                <i class="ti ti-history me-2"></i>
                                <strong>Parceria concluída.</strong> Obrigado pela colaboração com o projeto <strong>{{ $match->acao->titulo }}</strong>.
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="ti ti-ghost fs-1 mb-2"></i>
                                <p>Nenhuma parceria finalizada ainda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-edit-profile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        
        <form action="{{ route('vitrine.profile.update') }}" method="POST" class="m-0 w-100">
            @csrf
            @method('PUT')
            
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fs-3 text-brown"><i class="ti ti-edit me-2"></i> Editar Informações</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-2 text-start">
                    <h4 class="mb-2 border-bottom border-brown pb-2">Dados Gerais</h4>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Nome da Instituição</label>
                            <input type="text" class="form-control" name="nome" value="{{ old('nome', $instituicao->nome) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email de Contato / Login</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $instituicao->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="telefone_contato" value="{{ old('telefone_contato', $instituicao->telefone_contato) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">CNPJ</label>
                            <input type="text" class="form-control bg-light" value="{{ $instituicao->cnpj }}" disabled title="O CNPJ não pode ser alterado após o cadastro.">
                        </div>
                    </div>

                    <h4 class="mb-3 border-bottom border-brown pb-2">Endereço</h4>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">CEP</label>
                            <input type="text" class="form-control" name="cep" value="{{ old('cep', $instituicao->cep) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Logradouro</label>
                            <input type="text" class="form-control" name="logradouro" value="{{ old('logradouro', $instituicao->logradouro) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Número</label>
                            <input type="text" class="form-control" name="numero" value="{{ old('numero', $instituicao->numero) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Complemento</label>
                            <input type="text" class="form-control" name="complemento" value="{{ old('complemento', $instituicao->complemento) }}">
                        </div>
                    </div>

                    <h4 class="mb-3 border-bottom border-brown pb-2">Alterar Senha <span class="text-muted fs-5 fw-normal">(Opcional)</span></h4>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Senha Atual</label>
                            <input type="password" class="form-control" name="actual_password" placeholder="Preencha apenas se for alterar a senha">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" name="new_password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" name="new_password_confirmation">
                        </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-yellow fw-bold shadow-sm px-4 fs-4">
                        <i class="ti ti-device-floppy me-2"></i> Salvar Perfil
                    </button>
                </div>
            </div>
            
        </form>
    </div>
</div>
@endsection