@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      @can('adicionar_ação')
      <a href="{{route('actions.create')}}" class="btn">Inserir</a>
      @endcan
      @can('importar_ações')
      <a href="" class="btn">Importar</a>
      @endcan
      <bottom class="btn" data-bs-toggle="collapse" data-bs-target="#filtros" aria-expanded="false"
        aria-controls="collapseExample">Filtros</bottom>
    </div>
    <div class="d-flex justify-content-end col-12 col-md-6 p-0 m-0">
      <x-table.search route="{{ route('actions.index') }}"></x-table.search>
    </div>
  </div>
  <div class="collapse m-0 p-0 mb-3" id="filtros">
    <div class="card card-body m-0 p-3">
      <form action="{{ route('actions.index') }}" method="GET" class="row">
        @foreach ($parametros as $key => $parametro)
        <x-form-elements.select.select title="{{ ucfirst(strtolower($key)) }}" id="{{ strtolower($key) }}" name="{{ strtolower($key) }}"
          class="col-12 col-md-4 col-lg-3">

          <x-slot:options>
            <option value="" disabled {{ request(strtolower($key))=='' ? 'selected' : '' }}>Selecione</option>
            @foreach ($parametro as $lista)
            <option value="{{ $lista->value }}" {{ request(strtolower($key))==$lista->value ? 'selected' : '' }}>
              {{ $lista->value }}
            </option>
            @endforeach
          </x-slot:options>

        </x-form-elements.select.select>
        @endforeach

        <x-form-elements.select.select title="Status" id="status" name="status" class="col-12 col-md-4 col-lg-3">
          <x-slot:options>
            <option value="" disabled {{ request('status')===null ? 'selected' : '' }}>Selecione</option>
            <option value="00" {{ request('status')==='00' ? 'selected' : '' }}>Inativo</option>
            <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Ativo</option>
            <option value="2" {{ request('status')=='2' ? 'selected' : '' }}>Finalizado</option>
          </x-slot:options>
        </x-form-elements.select.select>

        @include('components.form-elements.input.input', [
          'title' => 'Ano',
          'type' => 'number',
          'class' => 'mb-3 col-12 col-md-4 col-lg-3',
          'name' => 'ano',
          'required' => 'false',
          'placeholder' => 'Ano',
          'value' => request('ano')
        ])

        <div class="col-12 col-md-4 col-lg-3">
          <label class="form-label d-block">&nbsp;</label>
          <div class="d-flex gap-2">
            <button class="btn btn-green w-100" type="submit">Filtrar</button>

            <a href="{{ route('actions.index') }}" class="btn btn-outline-secondary w-100">
              Limpar
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="table-responsive p-0">
    <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
      <thead>
        <tr>
          <th class="text-wrap" style="min-width: 400px;">titulo</th>
          <th>id_atividade</th>
          <th>id_projeto</th>
          <th>coordenador</th>
          <th>centro_departamento</th>
          <th>data_inicio</th>
          <th>data_fim</th>
          <th>ano</th>
          <th>tipo_acao</th>
          <th>area_tematica</th>
          <th>modalidade</th>
          <th>status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($actions as $item)
        <tr>
          <td class="text-wrap" style="min-width: 400px;">{{$item->titulo}}</td>
          <td>{{$item->id_atividade}}</td>
          <td>{{$item->id_projeto}}</td>
          <td>{{$item->coordenador->name}}</td>
          <td>{{$item->centro_departamento}}</td>
          <td>{{date('d-m-Y', strtotime($item->data_inicio))}}</td>
          <td>{{date('d-m-Y', strtotime($item->data_fim))}}</td>
          <td>{{$item->ano}}</td>
          <td>{{$item->tipo_acao}}</td>
          <td>{{$item->area_tematica}}</td>
          <td>{{$item->modalidade}}</td>
          <td>{{ $item->status == 0 ? 'Inativo' : ($item->status == 1 ? 'Ativo' : 'Finalizado') }}</td>
          <td>
            @can('editar_ação')
            <a href="">Editar</a></td>
            @endcan
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-center mt-5">
    {{ $actions->links() }}
  </div>
</div>
@endsection
@section('scripts')
@endsection