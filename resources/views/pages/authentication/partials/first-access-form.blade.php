<form action="{{ route('access.store') }}" method="post" autocomplete="off" novalidate>
  @csrf
  <div class="mb-3">
    <fieldset class="m-0 p-0 border-0">
      <legend class="form-label fw-semibold mb-3">Como você deseja participar das ações de extensão?</legend>
      <div class="first-access-choices">
        <label class="form-selectgroup-item first-access-choice">
          <input type="radio" name="is_external_institution" value="1" class="form-selectgroup-input" {{ old('is_external_institution') == '1' ? 'checked' : '' }}>
          <span class="form-selectgroup-label">
            <span class="fw-semibold">Sou uma instituição interessada nas ações de extensão da UFCA</span>
            <span class="d-block text-muted small mt-1">Cadastre sua instituição para participar das ações.</span>
          </span>
        </label>
        <label class="form-selectgroup-item first-access-choice">
          <input type="radio" name="is_external_institution" value="0" class="form-selectgroup-input" {{ old('is_external_institution') == '0' ? 'checked' : '' }}>
          <span class="form-selectgroup-label">
            <span class="fw-semibold">Sou da comunidade da UFCA</span>
            <span class="d-block text-muted small mt-1">Acesse com seu e-mail institucional ou cadastrado.</span>
          </span>
        </label>
      </div>
    </fieldset>
  </div>

  <div id="first-access-form-context" class="first-access-form-context d-none" aria-live="polite"></div>

  <div id="modal-form-internal" class="d-none">
    <div class="mb-3">
      <label class="form-label">Email institucional ou e-mail cadastrado no sigaa</label>
      <input type="email" class="form-control" placeholder="Digite seu email institucional" name="email"
        value="{{ old('email') }}" data-required>
    </div>
    <div class="mb-3">
      <label class="form-check">
        <input class="form-check-input" type="checkbox" name="aceite" data-required {{ old('aceite') ? 'checked' : '' }}>
        <span class="form-check-label">Concordo que li e aceito os <a href="{{ route('terms.index') }}"
            target="_blank">Termos de Uso e Política de Privacidade</a>.</span>
      </label>
    </div>
    <button type="submit" class="btn btn-primary w-100">Acessar</button>
  </div>

  <div id="modal-form-external" class="d-none">
    <div class="mb-3">
      <label class="form-label required">Nome da Instituição</label>
      <input type="text" class="form-control" placeholder="Digite o nome completo" name="nome" value="{{ old('nome') }}"
        data-required>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label required">E-mail</label>
        <input type="email" class="form-control" placeholder="Digite o e-mail" name="email" value="{{ old('email') }}"
          data-required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label required">CNPJ</label>
        <input type="text" class="form-control" placeholder="00.000.000/0000-00" id="modal-cnpj" name="cnpj"
          value="{{ old('cnpj') }}" data-required>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label required">CEP</label>
        <input type="text" class="form-control" placeholder="00000-000" id="modal-cep" name="cep"
          value="{{ old('cep') }}" data-required>
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
      <input type="tel" class="form-control" placeholder="(00) 00000-0000" id="modal-phone" name="telefone_contato"
        value="{{ old('telefone_contato') }}" data-required>
    </div>
    <div class="mb-3">
      <label class="form-check">
        <input class="form-check-input" type="checkbox" name="aceite" data-required {{ old('aceite') ? 'checked' : '' }}>
        <span class="form-check-label">Concordo que li e aceito os <a href="{{ route('terms.index') }}"
            target="_blank">Termos de Uso e Política de Privacidade</a>.</span>
      </label>
    </div>
    <button type="submit" class="btn btn-primary w-100">Acessar</button>
  </div>
  @if (session()->has('first_access_modal') || $errors->any())
    @include('components.message.message')
  @endif
</form>