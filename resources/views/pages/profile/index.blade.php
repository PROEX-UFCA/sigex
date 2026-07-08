@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-header">
  <div class="">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">
          <a href="{{ route('profile.index') }}">Perfil</a>
        </div>
        <h2 class="page-title">
          Informações pessoais
        </h2>
      </div>
      <div class="col-auto ms-auto">
      </div>
    </div>
  </div>
</div>
<div class="page-body row">
  <div class="col-12 col-md-8">
    <form action="{{ route('profile.store') }}" method="post" class="row">
      @csrf
      <div class="card">
        <div class="card-body row">
          <div class="card-title">Informações do Usuário</div>
          @include('components.form-elements.input.input', [
          'title' => 'Nome',
          'type' => 'text',
          'class' => 'mb-3 col-12',
          'name' => 'name',
          'placeholder' => 'Digite seu nome',
          'value' => $user->name,
          ])
          @include('components.form-elements.input.input', [
          'title' => 'Telefone',
          'type' => 'text',
          'class' => 'mb-3 col-12 col-md-6',
          'name' => 'phone',
          'id' => 'phone',
          'placeholder' => 'Digite seu telefone',
          'value' => $user->phone ?? '',
          ])
          @include('components.form-elements.input.input', [
          'title' => 'Matrícula ou Siape',
          'type' => 'text',
          'class' => 'mb-3 col-12 col-md-6',
          'name' => 'matricula_siape',
          'placeholder' => 'Digite sua matrícula ou siape',
          'value' => $user->matricula_siape ?? '',
          ])

          @foreach ($parametros as $key => $parametro)
          <x-form-elements.select.select title="{{ ucfirst(strtolower($key)) }}" id="{{ strtolower($key) }}"
            name="{{ strtolower($key) }}" class="col-12 col-md-6">

            <x-slot:options>
              <option value="" disabled {{ old(strtolower($key))=='' ? 'selected' : '' }}>Selecione</option>
              @foreach ($parametro as $lista)
              <option value="{{ $lista->value }}" {{ old(strtolower($key))==$lista->value ? 'selected' : '' }}>
                {{ $lista->value }}
              </option>
              @endforeach
            </x-slot:options>

          </x-form-elements.select.select>
          @endforeach
          <div class="d-flex w-100 justify-content-between mt-3">
            <button type="submit" class="btn btn-success ms-auto">Salvar alterações</button>
          </div>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-body row">
          <div class="card-title">Alterar senha</div>

          <div class="col-12 col-md-4">
            <label class="form-label">
              Senha Atual
            </label>
            <div class="input-group input-group-flat">
              <input type="password" class="form-control" name="actual_password" id="actual_password"
                placeholder="Digite sua senha atual" value="{{ old('actual_password') }}">
              <span class="input-group-text">
                <a href="#" class="link-secondary text-decoration-none" onclick="change('actual_password')">
                  <i class="ti ti-eye" style="font-size: 20px;"></i>
                </a>
              </span>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">
              Nova Senha
            </label>
            <div class="input-group input-group-flat">
              <input type="password" class="form-control" name="password" id="password"
                placeholder="Digite a nova senha" value="{{ old('password') }}">
              <span class="input-group-text">
                <a href="#" class="link-secondary text-decoration-none" onclick="change('password')">
                  <i class="ti ti-eye" style="font-size: 20px;"></i>
                </a>
              </span>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">
              Confirmar senha
            </label>
            <div class="input-group input-group-flat">
              <input type="password" class="form-control" name="password_confirm" id="password_confirm"
                placeholder="Confirme a nova senha" value="{{ old('password_confirm') }}">
              <span class="input-group-text">
                <a href="#" class="link-secondary text-decoration-none" onclick="change('password_confirm')">
                  <i class="ti ti-eye" style="font-size: 20px;"></i>
                </a>
              </span>
            </div>
          </div>

          <div class="d-flex w-100 justify-content-between mt-3">
            <button type="submit" class="btn btn-success ms-auto">Salvar alterações</button>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="col-12 col-md-4">
    <div class="card">
      <div class="card-body row">
        <div class="card-title">Informações do usuário</div>
        <div class="mb-2">
          <i class="icon me-2 text-secondary icon-2 ti ti-id-badge-2"></i>
          Nome: <strong>{{ $user->name }}</strong>
        </div>
        <div class="mb-2">
          <i class="icon me-2 text-secondary icon-2 ti ti-mail"></i>
          Email: <strong>{{ $user->email }}</strong>
        </div>
        <div class="mb-2">
          <i class="icon me-2 text-secondary icon-2 ti ti-users-group"></i>
          Tipo de usuário: <strong>{{ $user->roles->first()->name }}</strong>
        </div>
        <div class="mb-2">
          <i class="icon me-2 text-secondary icon-2 ti ti-phone"></i>
          Telefone: <strong>{{ $user->phone != NULL ? $user->phone : 'Não informado' }}</strong>
        </div>
        <div class="mb-2">
          <i class="icon me-2 text-secondary icon-2 ti ti-id-badge-2"></i>
          Matrícula/Siape: <strong>{{ $user->matricula_siape != null ? $user->matricula_siape : 'Não informado'
            }}</strong>
        </div>
        <div class="mb-2">
          <i class="icon me-2 text-secondary icon-2 ti ti-building-community"></i>
          Centro/Departamento: <strong>{{ $user->centro_departamento != null ? $user->centro_departamento : 'Não
            informado' }}</strong>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  function change(id) {
      const input = document.getElementById(id);

      if (input.type === 'password') {
        input.type = 'text';
      } else {
        input.type = 'password';
      }

    }

    $('#phone').mask('(00)00000-0000', {
      reverse: false
    });
</script>
@endsection