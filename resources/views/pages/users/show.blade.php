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
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@endsection