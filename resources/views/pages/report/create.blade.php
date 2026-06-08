@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      <a href="{{route('report.index')}}" class="btn">Voltar página</a>
    </div>
  </div>
  <div class="card form-fieldset">
    <form class="card-body row p-0" method="POST" action="{{ route('report.store') }}">
      @csrf
      <div class="col-12 row m-0 p-0">

        @include('components.form-elements.input.input', [
        'title' => 'Título do relatório',
        'type' => 'text',
        'class' => 'mb-3 col-12 col-md-4',
        'name' => 'titulo',
        'required' => 'true',
        'placeholder' => 'Digite o nome do relatório.',
        'value' => old('titulo') ?? '',
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Data de início',
        'type' => 'datetime-local',
        'class' => 'mb-3 col-12 col-md-2',
        'name' => 'data_inicio',
        'required' => 'true',
        'value' => old('data_inicio') ?? '',
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Prazo',
        'type' => 'datetime-local',
        'class' => 'mb-3 col-12 col-md-2',
        'name' => 'prazo',
        'required' => 'true',
        'value' => old('prazo') ?? '',
        ])

        <x-form-elements.select.select title="Formulário" id="formulario" name="formulario" class="col-12 col-md-4"
            required="true">
            <x-slot:options>
              <option value="" disabled {{ request('formulario')===null ? 'selected' : '' }}>Selecione</option>
              @foreach ($formularios as $formulario)
              <option value="{{ $formulario->id }}" {{ old('formulario')==$formulario->id ? 'selected' : '' }}>{{ $formulario->titulo }}</option>
              @endforeach
            </x-slot:options>
          </x-form-elements.select.select>

        <div class="col-12">
          <hr class="my-3">
          <p class="fw-bold mb-3">Parâmetros de disponibilização</p>
        </div>

        <div class="row col-12 m-0 p-0">
          @foreach ($parametros as $key => $parametro)
          <div class="col-12 col-md-4 mb-3">
            <div class="card p-3 h-100">
              <label class="form-label required">{{ ucfirst(strtolower($key)) }}</label>
              @foreach ($parametro as $item)
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $item->value }}" name="parametros[{{strtolower($key)}}][]">
                <span class="form-check-label">{{ $item->value }}</span>
              </label>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>

        <div class="col-12">
          <hr class="my-3">
        </div>

        <div class="row col-12 m-0 p-0 mb-3">
          <x-form-elements.select.select title="Ano da ação" id="ano_acao" name="ano_acao"
            class="col-12 col-md-4 mb-3 mb-md-0" required="true">
            <x-slot:options>
              <option value="" disabled {{ request('ano_acao')===null ? 'selected' : '' }}>Selecione</option>
              @foreach ($years as $year)
              <option value="{{ $year }}" {{ old('ano_acao')==$year ? 'selected' : '' }}>{{ $year }}</option>
              @endforeach
            </x-slot:options>
          </x-form-elements.select.select>

          <x-form-elements.select.select title="Ano do início da ação" id="ano_inicio" name="ano_inicio"
            class="col-12 col-md-4 mb-3 mb-md-0" required="true">
            <x-slot:options>
              <option value="" disabled {{ request('ano_inicio')===null ? 'selected' : '' }}>Selecione</option>
              @foreach ($years as $year)
              <option value="{{ $year }}" {{ old('ano_inicio')==$year ? 'selected' : '' }}>{{ $year }}</option>
              @endforeach
            </x-slot:options>
          </x-form-elements.select.select>

          <x-form-elements.select.select title="Ano do fim da ação" id="ano_fim" name="ano_fim" class="col-12 col-md-4"
            required="true">
            <x-slot:options>
              <option value="" disabled {{ request('ano_fim')===null ? 'selected' : '' }}>Selecione</option>
              @foreach ($years as $year)
              <option value="{{ $year }}" {{ old('ano_fim')==$year ? 'selected' : '' }}>{{ $year }}</option>
              @endforeach
            </x-slot:options>
          </x-form-elements.select.select>
        </div>

        <div class="col-12 d-flex justify-content-end mt-2">
          <button type="submit" class="btn btn-green px-4">
            Salvar
          </button>
        </div>

      </div>
    </form>
  </div>
</div>
@endsection
@section('scripts')
{{-- <script src="{{ asset('assets/libs/tom-select/dist/js/tom-select.base.min.js') }}" defer></script>
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
</script> --}}
@endsection