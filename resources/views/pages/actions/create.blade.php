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
      'title' => 'Id da atividade',
      'type' => 'text',
      'class' => 'mb-3 col-12 col-md-3',
      'name' => 'id_atividade',
      'required' => 'true',
      'placeholder' => 'Digite o id da atividade',
      'value' => old('id_atividade') ?? '',
      ])

      @include('components.form-elements.input.input', [
      'title' => 'Id do projeto',
      'type' => 'text',
      'class' => 'mb-3 col-12 col-md-3',
      'name' => 'id_projeto',
      'required' => 'true',
      'placeholder' => 'Digite o id do projeto',
      'value' => old('id_projeto') ?? '',
      ])

      <div class="mb-3 col-12 col-md-6">
        <label class="form-label required">Coordenador</label>
        <select class="form-select" id="teachers" name="id_coordenador">
          <option value="">Selecione</option>
          @foreach ($coordinators as $coordinator)
          <option value="{{ $coordinator->uuid }}" {{ old('id_coordenador') ? (old('id_coordenador')==$coordinator->id ?
            'selected' : '') : ' '
            }}>
            {{ $coordinator->name }}</option>
          @endforeach
        </select>
      </div>

      @foreach ($parametros as $key => $parametro)
      <x-form-elements.select.select title="{{ ucfirst(strtolower($key)) }}" id="{{ strtolower($key) }}"
        name="{{ strtolower($key) }}" class="col-12 col-md-4 col-lg-3">

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

      <x-form-elements.select.select title="Status" id="status" name="status" class="col-12 col-md-4 col-lg-3">
        <x-slot:options>
          <option value="" disabled {{ request('status')===null ? 'selected' : '' }}>Selecione</option>
          <option value="0" {{ old('status')==='00' ? 'selected' : '' }}>Inativo</option>
          <option value="1" {{ old('status')=='1' ? 'selected' : '' }}>Ativo</option>
          <option value="2" {{ old('status')=='2' ? 'selected' : '' }}>Finalizado</option>
        </x-slot:options>
      </x-form-elements.select.select>

      @include('components.form-elements.input.input', [
      'title' => 'Ano',
      'type' => 'number',
      'class' => 'mb-3 col-12 col-md-4 col-lg-3',
      'name' => 'ano',
      'required' => 'false',
      'placeholder' => 'Ano',
      'value' => old('ano')
      ])

      @include('components.form-elements.input.input', [
      'title' => 'Data de início',
      'type' => 'date',
      'class' => 'mb-3 col-12 col-md-4 col-lg-3',
      'name' => 'data_inicio',
      'required' => 'true',
      'value' => old('data_inicio') ?? '',
      ])

      @include('components.form-elements.input.input', [
      'title' => 'Data de término',
      'type' => 'date',
      'class' => 'mb-3 col-12 col-md-4 col-lg-3',
      'name' => 'data_fim',
      'required' => 'true',
      'value' => old('data_fim') ?? '',
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