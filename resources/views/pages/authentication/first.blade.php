@extends('templates.auth')

@section('content')
  <div class="page page-center">
    <div class="container container-tight py-4">
      <div class="card card-md bg-transparent shadow-none border-0">
        <div class="card-body">
          <h2 class="h2 text-center mb-4">Primeiro acesso</h2>
          <form action="{{ route('access.store') }}" method="post" autocomplete="off" novalidate>
            @csrf
            <div class="mb-3">
              <label class="form-label">
                Email institucional ou email cadastrado no sigaa
              </label>
              <div class="input-group input-group-flat">
                <input type="email" class="form-control" placeholder="Digite sua email institucional" name="email"
                  id="email" value="{{ old('email') }}" required>
              </div>
            </div>
            <div class="">
              <label class="form-check">
                <input class="form-check-input" type="checkbox" name="aceite" required>
                <span class="form-check-label">
                  Concordo que li e aceito os
                  <a href="{{ route('terms.index') }}" target="_blank">Termos de Uso e Política de Privacidade</a>.
                </span>
              </label>
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Acessar</button>
            </div>
            @include('components.message.message')
          </form>
          <div class="text-center text-muted mt-3">
            Esquecer, <a href="{{ route('login') }}">me envie de volta</a> para a página de login.
          </div>
        </div>
      </div>
      <div class="text-center text-muted mt-3">
        Desenvolvido por <a href="https://github.com/Otavio-Ferreira" tabindex="-1">Otavio &copy2025</a>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
@endsection
