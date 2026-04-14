@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 row mb-4">
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
    <div class="card">
      <div class="card-body">
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
    </div>
  </div>
  <div class="col-12 col-md-8">
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap justify-content-between mb-2">
          <h3 class="m-0">Equipe da ação</h3>
          <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#modal-add-equipe"
            aria-controls="offcanvasExample">
            Inserir
          </button>
          <x-modal.offcanvas route="{{ route('actions.storeTeam', $action->id) }}" id="modal-add-equipe"
            class="offcanvas-end" title="Adicionar um usuário à equipe">
            <x-slot:content>
              <div class="mb-3">
                <label class="form-label required">Usuário</label>
                <select class="form-select" id="teachers" name="id_usuario">
                  <option value="">Selecione</option>
                  @foreach ($coordinators as $coordinator)
                  <option value="{{ $coordinator->uuid }}" {{ old('id_usuario') ? (old('id_usuario')==$coordinator->
                    id ?
                    'selected' : '') : ' '
                    }}>
                    {{ $coordinator->name .' - '. $coordinator->matricula_siape}}</option>
                  @endforeach
                </select>
              </div>
              <x-form-elements.select.select title="Categoria" id="categoria" name="categoria" class="">
                <x-slot:options>
                  <option value="" disabled {{ old('categoria')=='' ? 'selected' : '' }}>Selecione</option>
                  @foreach ($categorias as $categoria)
                  <option value="{{ $categoria->value }}" {{ old('categoria')==$categoria->value ? 'selected' : '' }}>
                    {{ $categoria->value }}
                  </option>
                  @endforeach
                </x-slot:options>
              </x-form-elements.select.select>
            </x-slot:content>
          </x-modal.offcanvas>
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
              @foreach ($action->equipe as $item)
              <tr>
                <td>{{ $item->user->name }}</td>
                <td>{{ $item->user->email }}</td>
                <td>{{ $item->user->phone ?? '-' }}</td>
                <td>{{ $item->user->matricula_siape ?? '-' }}</td>
                <td>{{ $item->user->centro_departamento ?? '-' }}</td>
                <td>{{ $item->categoria }}</td>
                <td><a href="" class="text-danger">Remover</a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center flex-wrap justify-content-between mb-2">
          <h3 class="m-0">Agenda da ação</h3>
          <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#modal-add-agenda"
            aria-controls="offcanvasExample">
            Inserir
          </button>
          <x-modal.offcanvas route="{{ route('actions.storeSchedule', $action->id) }}" id="modal-add-agenda"
            class="offcanvas-end" title="Adicionar agenda">
            <x-slot:content>
              @include('components.form-elements.input.input', [
              'title' => 'Título do evento',
              'type' => 'text',
              'class' => 'mb-3',
              'name' => 'titulo',
              'required' => 'true',
              'placeholder' => 'Digite o título do evento',
              'value' => old('titulo') ?? '',
              ])
              @include('components.form-elements.input.input', [
              'title' => 'Local ou formato do evento',
              'type' => 'text',
              'class' => 'mb-3',
              'name' => 'local_formato',
              'required' => 'true',
              'placeholder' => 'Digite o local ou formato do evento',
              'value' => old('local_formato') ?? '',
              ])
              @include('components.form-elements.input.input', [
              'title' => 'Data e hora de início do evento',
              'type' => 'datetime-local',
              'class' => 'mb-3',
              'name' => 'data_hora_inicio',
              'required' => 'true',
              'placeholder' => 'Digite a data e hora de início',
              'value' => old('data_hora_inicio') ?? '',
              ])
              @include('components.form-elements.input.input', [
              'title' => 'Data e hora do fim do evento',
              'type' => 'datetime-local',
              'class' => 'mb-3',
              'name' => 'data_hora_fim',
              'required' => 'true',
              'placeholder' => 'Digite a data e hora do fim',
              'value' => old('data_hora_fim') ?? '',
              ])
              @include('components.form-elements.textarea.textarea', [
              'title' => 'Descrição do evento',
              'class' => 'mb-3',
              'name' => 'descricao',
              'required' => 'true',
              'placeholder' => 'Descricao do evento',
              'value' => old('descricao') ?? '',
              ])
            </x-slot:content>
          </x-modal.offcanvas>
        </div>
        <div class="table-responsive p-0 mb-3">
          <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
            <thead>
              <th>Evento</th>
              <th>Data/Hora de início</th>
              <th>Data/Hora de fim</th>
              <th>Local</th>
              <th>Descrição</th>
              <th></th>
              <th></th>
            </thead>
            <tbody>
              @foreach ($action->agenda as $item)
              <tr>
                <td>{{ $item->titulo_evento }}</td>
                <td>{{ $item->data_hora_inicio }}</td>
                <td>{{ $item->data_hora_fim }}</td>
                <td>{{ $item->local_formato }}</td>
                <td>{{ $item->descricao }}</td>
                <td><a href="">Editar</a></td>
                <td><a href="" class="text-danger">Remover</a></td>
              </tr>
              @endforeach
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