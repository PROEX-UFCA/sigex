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
            <label class="form-label">Você é uma instituição externa?</label>
            <div class="form-selectgroup">
              <label class="form-selectgroup-item">
                <input type="radio" name="is_external_institution" value="1" class="form-selectgroup-input" {{
                  old('is_external_institution')=='1' ? 'checked' : '' }}>
                <span class="form-selectgroup-label">Sim</span>
              </label>
              <label class="form-selectgroup-item">
                <input type="radio" name="is_external_institution" value="0" class="form-selectgroup-input" {{
                  old('is_external_institution')=='0' ? 'checked' : '' }}>
                <span class="form-selectgroup-label">Não</span>
              </label>
            </div>
          </div>

          <!-- FORMULÁRIO NÃO EXTERNO (MANTIDO CONFORME ORIGINAL) -->
          <div id="form-internal" class="d-none">
            <div class="mb-3">
              <label class="form-label">Email institucional ou e-mail cadastrado no sigaa</label>
              <div class="input-group input-group-flat">
                <input type="email" class="form-control" placeholder="Digite seu email institucional" name="email"
                  value="{{ old('email') }}" data-required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-check">
                <input class="form-check-input" type="checkbox" name="aceite" data-required {{ old('aceite') ? 'checked'
                  : '' }}>
                <span class="form-check-label">
                  Concordo que li e aceito os
                  <a href="{{ route('terms.index') }}" target="_blank">Termos de Uso e Política de Privacidade</a>.
                </span>
              </label>
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Acessar</button>
            </div>
          </div>

          <!-- FORMULÁRIO EXTERNO (NOVOS CAMPOS) -->
          <div id="form-external" class="d-none">
            <div class="mb-3">
              <label class="form-label required">Nome da Instituição</label>
              <input type="text" class="form-control" placeholder="Digite o nome completo" name="nome"
                value="{{ old('nome') }}" data-required>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label required">E-mail</label>
                <input type="email" class="form-control" placeholder="Digite o e-mail" name="email"
                  value="{{ old('email') }}" data-required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label required">CNPJ</label>
                <input type="text" class="form-control" placeholder="00.000.000/0000-00" id="cnpj" name="cnpj"
                  value="{{ old('cnpj') }}" data-required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label required">CEP</label>
                <input type="text" class="form-control" placeholder="00000-000" id="cep" name="cep" value="{{ old('cep') }}"
                  data-required>
              </div>
              <div class="col-md-8 mb-3">
                <label class="form-label required">Logradouro</label>
                <input type="text" class="form-control" placeholder="Rua, Avenida, etc." name="logradouro"
                  value="{{ old('logradouro') }}" data-required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label required">Número</label>
                <input type="text" class="form-control" placeholder="Número" name="numero" value="{{ old('numero') }}"
                  data-required>
              </div>
              <div class="col-md-8 mb-3">
                <label class="form-label">Complemento <span class="form-label-description">Opcional</span></label>
                <input type="text" class="form-control" placeholder="Apto, Sala, Bloco, etc." name="complemento"
                  value="{{ old('complemento') }}">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label required">Telefone de Contato</label>
              <input type="tel" class="form-control" placeholder="(00) 00000-0000" id="phone" name="telefone_contato"
                value="{{ old('telefone_contato') }}" data-required>
            </div>

            <div class="mb-3">
              <label class="form-check">
                <input class="form-check-input" type="checkbox" name="aceite" data-required {{ old('aceite') ? 'checked'
                  : '' }}>
                <span class="form-check-label">
                  Concordo que li e aceito os
                  <a href="{{ route('terms.index') }}" target="_blank">Termos de Uso e Política de Privacidade</a>.
                </span>
              </label>
            </div>

            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Acessar</button>
            </div>
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
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('input[name="is_external_institution"]');
    const formInternal = document.getElementById('form-internal');
    const formExternal = document.getElementById('form-external');

    function toggleForms(value) {
      if (value == '0') { // Não é instituição
        formInternal.classList.remove('d-none');
        formExternal.classList.add('d-none');
        
        toggleInputs(formInternal, true);  // Ativa e exige inputs internos
        toggleInputs(formExternal, false); // Oculta e desabilita inputs externos
      } else if (value == '1') { // É instituição
        formExternal.classList.remove('d-none');
        formInternal.classList.add('d-none');
        
        toggleInputs(formExternal, true);  // Ativa e exige inputs externos
        toggleInputs(formInternal, false); // Oculta e desabilita inputs internos
      }
    }

    function toggleInputs(container, enable) {
      const inputs = container.querySelectorAll('input, select, textarea');
      inputs.forEach(input => {
        if (enable) {
          input.removeAttribute('disabled');
          if (input.hasAttribute('data-required')) {
            input.setAttribute('required', 'required');
          }
        } else {
          input.setAttribute('disabled', 'disabled'); // Impede o envio no POST
          input.removeAttribute('required');
        }
      });
    }

    radios.forEach(radio => {
      radio.addEventListener('change', (e) => toggleForms(e.target.value));
        if (radio.checked) {
          toggleForms(radio.value);
        }
      });
  });

  $('#cnpj').mask('00.000.000/0000-00', {
      reverse: false
  });
  $('#phone').mask('(00) 00000-0000', {
      reverse: false
  });
  $('#cep').mask('00000-000', {
      reverse: false
  });
</script>
@endsection