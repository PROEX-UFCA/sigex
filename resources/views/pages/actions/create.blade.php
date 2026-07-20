@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      <a href="{{route('actions.index')}}" class="btn">Voltar página</a>
    </div>
  </div>
  <div class="card form-fieldset">
    <form class="card-body row p-0" method="POST" action="{{route('actions.store')}}">
      @csrf
      <div class="col-12 col-md-7 row">
        @include('components.form-elements.input.input', [
        'title' => 'Título',
        'type' => 'text',
        'class' => 'mb-3 col-12',
        'name' => 'titulo',
        'required' => 'true',
        'placeholder' => 'Digite o título',
        'value' => old('titulo') ?? '',
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Palavras chave',
        'type' => 'text',
        'class' => 'mb-3 col-12 col-md-6',
        'name' => 'palavras_chave',
        'required' => 'true',
        'placeholder' => 'Digite as palavras chaves',
        'value' => old('palavras_chave') ?? '',
        ])

        <div class="mb-3 col-12 col-md-6">
          <label class="form-label required">Coordenador</label>
          <select class="form-select" id="teachers" name="id_coordenador">
            <option value="">Selecione</option>
            @foreach ($coordinators as $coordinator)
            <option value="{{ $coordinator->uuid }}" {{ old('id_coordenador') ? (old('id_coordenador')==$coordinator->id
              ?
              'selected' : '') : ' '
              }}>
              {{ $coordinator->name }}</option>
            @endforeach
          </select>
        </div>

        @include('components.form-elements.input.input', [
        'title' => 'Id do projeto',
        'type' => 'text',
        'class' => 'mb-3 col-12 col-md-4',
        'name' => 'id_projeto',
        'required' => 'true',
        'placeholder' => 'Digite o id do projeto',
        'value' => old('id_projeto') ?? '',
        ])

        @foreach ($parametros as $key => $parametro)
        <x-form-elements.select.select title="{{ str_replace('_', ' ',ucfirst(strtolower($key))) }}"
          id="{{ strtolower($key) }}" name="{{ strtolower($key) }}" class="col-12 col-md-4 col-lg-4" required="true">

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

        <x-form-elements.select.select title="Financiamento interno?" id="financiamento_interno" name="financiamento_interno"
          class="col-12 col-md-4 col-lg-4" required="true">
          <x-slot:options>
            <option value="" disabled {{ request('financiamento_interno')===null ? 'selected' : '' }}>Selecione</option>
            <option value="SIM" {{ old('financiamento_interno')=='SIM' ? 'selected' : '' }}>Sim</option>
            <option value="NÃO" {{ old('financiamento_interno')=='NÃO' ? 'selected' : '' }}>Não</option>
          </x-slot:options>
        </x-form-elements.select.select>

        <x-form-elements.select.select title="Financiamento externo?" id="financiamento_externo" name="financiamento_externo"
          class="col-12 col-md-4 col-lg-4" required="true">
          <x-slot:options>
            <option value="" disabled {{ request('financiamento_externo')===null ? 'selected' : '' }}>Selecione</option>
            <option value="SIM" {{ old('financiamento_externo')=='SIM' ? 'selected' : '' }}>Sim</option>
            <option value="NÃO" {{ old('financiamento_externo')=='NÃO' ? 'selected' : '' }}>Não</option>
          </x-slot:options>
        </x-form-elements.select.select>

        @include('components.form-elements.input.input', [
        'title' => 'Bolsas solicitadas',
        'type' => 'number',
        'class' => 'mb-3 col-12 col-md-4 col-lg-4',
        'name' => 'bolsas_solicitadas',
        'required' => 'true',
        'placeholder' => 'Bolsas solicitadas',
        'min' => '1',
        'value' => old('bolsas_solicitadas')
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Bolsas concedidas',
        'type' => 'number',
        'class' => 'mb-3 col-12 col-md-4 col-lg-4',
        'name' => 'bolsas_concedidas',
        'required' => 'true',
        'placeholder' => 'Bolsas concedidas',
        'min' => '1',
        'value' => old('bolsas_concedidas')
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Ano',
        'type' => 'number',
        'class' => 'mb-3 col-12 col-md-4 col-lg-4',
        'name' => 'ano',
        'required' => 'true',
        'placeholder' => 'Ano',
        'min' => '1000',
        'value' => old('ano')
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Data de início',
        'type' => 'date',
        'class' => 'mb-3 col-12 col-md-4 col-lg-4',
        'name' => 'data_inicio',
        'required' => 'true',
        'value' => old('data_inicio') ?? '',
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Data de término',
        'type' => 'date',
        'class' => 'mb-3 col-12 col-md-4 col-lg-4',
        'name' => 'data_fim',
        'required' => 'true',
        'value' => old('data_fim') ?? '',
        ])
      </div>

      <div class="col-12 m-auto col-md-5 row p-0">
        <div class="mb-3 card p-3">
          <label for="" class="form-label required">Ods da ONU</label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="1" name="ods[]">
            <span class="form-check-label">Erradicação da pobreza.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="2" name="ods[]">
            <span class="form-check-label">Fome zero e agricultura sustentável.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="3" name="ods[]">
            <span class="form-check-label">Saúde e bem-estar.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="4" name="ods[]">
            <span class="form-check-label">Educação de qualidade.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="5" name="ods[]">
            <span class="form-check-label">Igualdade de gênero.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="6" name="ods[]">
            <span class="form-check-label">Água limpa e saneamento.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="7" name="ods[]">
            <span class="form-check-label">Energia limpa e acessível.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="8" name="ods[]">
            <span class="form-check-label">Trabalho de decente e crescimento econômico.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="9" name="ods[]">
            <span class="form-check-label">Inovação infraestrutura.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="10" name="ods[]">
            <span class="form-check-label">Redução das desigualdades.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="11" name="ods[]">
            <span class="form-check-label">Cidades e comunidades sustentáveis.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="12" name="ods[]">
            <span class="form-check-label">Consumo e produção responsáveis.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="13" name="ods[]">
            <span class="form-check-label">Ação contra a mudança global do clima.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="14" name="ods[]">
            <span class="form-check-label">Vida na água.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="15" name="ods[]">
            <span class="form-check-label">Vida terrestre.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="16" name="ods[]">
            <span class="form-check-label">Paz, justiça e instituições eficazes.</span>
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="17" name="ods[]">
            <span class="form-check-label">Parcerias e meios de implementação.</span>
          </label>
        </div>
      </div>

      @include('components.form-elements.textarea.textarea', [
      'title' => 'Resumo',
      'type' => 'date',
      'class' => 'mb-3 col-12',
      'name' => 'resumo',
      'required' => 'true',
      'rows' => '5',
      'value' => old('resumo') ?? '',
      'placeholder' => 'Resumo do projeto',
      ])

      <div class="d-flex justify-content-end w-100">
        <button type="submit" class="btn btn-green">
          Salvar
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('assets/libs/tom-select/dist/js/tom-select.base.min.js') }}" defer></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
      var el3 = document.getElementById('teachers');
      if (el3) {
        new TomSelect(el3, {
          copyClassesToDropdown: false,
          dropdownParent: 'body',
          controlInput: '<input>',
          render: {
            item: function(data, escape) {
              return `<div>${escape(data.text)}</div>`;
            },
            option: function(data, escape) {
              return `<div>${escape(data.text)}</div>`;
            }
          }
        });
      }
    });
</script>
@endsection