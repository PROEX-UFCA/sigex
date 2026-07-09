@extends('templates.template')

@section('styles')
@endsection

@section('content')
<div class="page-body">

  <div class="mb-4">
    <a href="{{ route('users.index') }}" class="btn">Voltar página</a>
  </div>

  <div class="row">
    
    <div class="col-12 col-lg-8 mb-4">

      <div class="card">
        <div class="card-body">
          <h3 class="card-title mb-3">Informações do Usuário:</h3>
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
                <tr>
                  <td class="fw-bold">Grupo de Permissões</td>
                  <td>
                    @if($user->roles->isNotEmpty())
                    <span class="badge bg-primary">{{ ucfirst($user->roles->first()->name) }}</span>
                    @else
                    <span class="badge bg-secondary">Sem grupo</span>
                    @endif
                  </td>
                </tr>
              </tbody>
            </table>

          </div>
        </div>
      </div>

      @can("editar_usuário")
      @if(auth()->user()->uuid !== $user->uuid)
      <button type="button" class="btn btn-outline-danger p-2 mt-3" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $user->uuid }}">
        Deletar Usuário
      </button>
      @endif
      @endcan

    </div>

    <div class="col-12 col-lg-4">
      @can("editar_usuário")
      @if(auth()->user()->uuid !== $user->uuid)
      <div class="card">
        <div class="card-body">
          <h3 class="mb-3">Alterar Grupo de Permissões</h3>
          
          <form action="{{ route('users.updateRole', $user->uuid) }}" method="POST">
              @csrf
              @method('PUT')
              
              <div class="mb-3">
                  <select class="form-select" id="role" name="role" required>
                      <option value="" disabled>Selecione um grupo...</option>
                      @foreach($roles as $role)
                          <option value="{{ $role->name }}" 
                              {{ ($user->roles->first()->name ?? '') === $role->name ? 'selected' : '' }}>
                              {{ ucfirst($role->name) }}
                          </option>
                      @endforeach
                  </select>
              </div>
              
              <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary">Salvar Novo Grupo</button>
              </div>
          </form>
        </div>
      </div>
      @endif
      @endcan
    </div>

  </div>
  @can("editar_usuário")
  @if(auth()->user()->uuid !== $user->uuid)
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
    </div>
  @endif
  @endcan

</div>
@endsection

@section('scripts')
@endsection