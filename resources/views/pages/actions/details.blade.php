@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      <a href="{{route('actions.my')}}" class="btn">Voltar página</a>
    </div>
  </div>
  {{-- <div class="collapse m-0 p-0 mb-3" id="filtros">
    <div class="card card-body m-0 p-3">
      <form action="{{ route('actions.index') }}" method="GET" class="row">
        @foreach ($parametros as $key => $parametro)
        <x-form-elements.select.select title="{{ ucfirst(strtolower($key)) }}" id="{{ strtolower($key) }}"
          name="{{ strtolower($key) }}" class="col-12 col-md-4 col-lg-3">

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
  </div> --}}
  <div class="col-12 col-md-4">
    <h3>Informações sobre a ação:</h3>
    <div class="table-responsive p-0">
      <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
        <tbody>
          <tr>
            <td class="fw-bold">Título</td>
            <td>{{$action->titulo}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Id Atividade</td>
            <td>{{$action->id_atividade}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Id Projeto</td>
            <td>{{$action->id_projeto}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Coordenador</td>
            <td>{{$action->coordenador->name}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Centro/Departamento</td>
            <td>{{$action->centro_departamento}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Data Início</td>
            <td>{{date('d-m-Y', strtotime($action->data_inicio))}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Data Fim</td>
            <td>{{date('d-m-Y', strtotime($action->data_fim))}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Ano</td>
            <td>{{$action->ano}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Tipo Acao</td>
            <td>{{$action->tipo_acao}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Area Tematica</td>
            <td>{{$action->area_tematica}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Modalidade</td>
            <td>{{$action->modalidade}}</td>
          </tr>
          <tr>
            <td class="fw-bold">Status</td>
            <td>{{ $action->status == 0 ? 'Inativo' : ($action->status == 1 ? 'Ativo' : 'Finalizado') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-12 col-md-8">
    <div class="d-flex align-items-center flex-wrap justify-content-between mb-2">
      <h3 class="m-0">Equipe da ação</h3>
      <button class="btn">Inserir</button>
    </div>
    <div class="table-responsive p-0 mb-5">
      <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
        <thead>
          <th>Nome</th>
          <th>Email</th>
          <th>Telefone</th>
          <th>Matricula/Siape</th>
          <th>Centro/Departamento</th>
          <th>Categoria</th>
          <th></th>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
    <hr class="m-0 mb-3">
    <div class="d-flex align-items-center flex-wrap justify-content-between mb-2">
      <h3 class="m-0">Agenda da ação</h3>
      <button class="btn">Inserir</button>
    </div>
    <div class="table-responsive p-0 mb-3">
      <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
        <thead>
          <th>Evento</th>
          <th>Email</th>
          <th>Data/Hora de início</th>
          <th>Data/Hora de fim</th>
          <th>Local</th>
          <th>Descrição</th>
          <th></th>
          <th></th>
        </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
@section('scripts')
@endsection