@extends('templates.template')

@section('styles')
@endsection

@section('content')
<div class="page-body row">
  <!-- Botão de Voltar -->
  <div class="m-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      <a href="{{ route('users.index') }}" class="btn">Voltar página</a>
    </div>
  </div>

  <!-- Card de Informações do Usuário -->
  <div class="col-12 col-md-6">
    <div class="card">
      <div class="card-body">
        <h3>Informações do Usuário:</h3>
        <div class="table-responsive p-0">
          <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
            <tbody>
              <tr>
                <td class="fw-bold">Nome</td>
                <td>{{ $user->name }}</td>
              </tr>
              <tr>
                <td class="fw-bold">E-mail</td>
                <td>{{ $user->email }}</td>
              </tr>
              <tr>
                <td class="fw-bold">Telefone</td>
                <td>{{ $user->phone ?? '-' }}</td>
              </tr>
              <tr>
                <td class="fw-bold">Centro/Departamento</td>
                <td>{{ $user->centro_departamento ?? '-' }}</td>
              </tr>
              <tr>
                <td class="fw-bold">Matrícula/SIAPE</td>
                <td>{{ $user->matricula_siape ?? '-' }}</td>
              </tr>
              <tr>
                <td class="fw-bold">Status</td>
                <td>
                    @if($user->status == 1)
                        <span class="badge bg-success">Ativo</span>
                    @else
                        <span class="badge bg-danger">Inativo</span>
                    @endif
                </td>
              </tr>
            </tbody>
          </table>
        @if(auth()->user()->uuid !== $user->uuid)
        <button type="button" class="btn btn-outline-danger p-2 m-2" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $user->uuid }}">
          Deletar
        </button>
        
        <div class="modal fade" id="modalDelete{{ $user->uuid }}" tabindex="-1" aria-labelledby="modalLabel{{ $user->uuid }}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalLabel{{ $user->uuid }}">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              
              <div class="modal-body text-start text-wrap">
                Tem certeza que deseja deletar o usuário <strong>{{ $user->name }}</strong>? <br><br>
                <span class="text-muted small">Esta ação removerá o usuário do sistema e ele não terá mais acesso ao SIGEX.</span>
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                
                <form action="{{ route('users.destroy', $user->uuid) }}" method="POST" class="m-0 p-0">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger">Sim, Deletar Usuário</button>
                </form>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection

@section('scripts')
@endsection