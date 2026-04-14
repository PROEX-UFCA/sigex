@extends('templates.template')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/kanban/dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/kanban/styleDataTable.css') }}">
<style>
  #table thead th {
    white-space: nowrap;
    width: auto;
  }

  #table tbody td {
    white-space: nowrap;
  }

  #table {
    table-layout: fixed;
    width: 100%;
  }
</style>
@endsection
@section('content')
<div class="page-body">
  <div class="m-0 p-0 row mb-4">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      @can('adicionar_usuário')
      <a href="{{ route('users.create') }}" class="btn">Inserir</a>
      @endcan
      <bottom class="btn" data-bs-toggle="collapse" data-bs-target="#filtros" aria-expanded="false"
        aria-controls="collapseExample">Filtros</bottom>
    </div>
    <div class="d-flex justify-content-end col-12 col-md-6 p-0 m-0">
      <x-table.search route="{{ route('users.index') }}"></x-table.search>
    </div>
  </div>
  <div class="collapse m-0 p-0 mb-3" id="filtros">
    <div class="card card-body m-0 p-3">
      <form action="{{ route('users.index') }}" method="GET" class="row">
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

        <x-form-elements.select.select title="Instituições" id="instituicao" name="instituicao"
          class="col-12 col-md-4 col-lg-3">
          <x-slot:options>
            <option value="" disabled {{ request('instituicao')===null ? 'selected' : '' }}>Selecione</option>
            <option value="false" {{ request('instituicao')=='false' ? 'selected' : '' }}>Não</option>
            <option value="true" {{ request('instituicao')=='true' ? 'selected' : '' }}>Sim</option>
          </x-slot:options>
        </x-form-elements.select.select>

        <div class="col-12 col-md-4 col-lg-3">
          <label class="form-label d-block">&nbsp;</label>
          <div class="d-flex gap-2">
            <button class="btn btn-green w-100" type="submit">Filtrar</button>

            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
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
          <th>Nome</th>
          <th>Email</th>
          <th>Instituição?</th>
          <th>Cpf</th>
          <th>Telefone</th>
          <th>centro_departamento</th>
          <th>matricula_siape</th>
          <th>Grupo</th>
          <th>Status</th>
          <th width="5%"></th>
          <th width="5%"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($users as $user)
        <tr>
          <td>{{$user->name}}</td>
          <td>{{$user->email}}</td>
          <td>{{ $user->id_instituicao == null ? 'Não' : 'Sim' }}</td>
          <td>{{ $user->cpf ?? "-" }}</td>
          <td>{{ $user->phone ?? "-" }}</td>
          <td>{{ $user->centro_departamento ?? "-" }}</td>
          <td>{{ $user->matricula_siape ?? "-" }}</td>
          <td>{{ $user->roles->first()->name ?? "-" }}</td>
          <td>{{ $user->status == 0 ? 'Inativo' : 'Ativo' }}</td>
          <td>
            @can('editar_usuário')
            <a href="">Editar</a>
            @endcan
          </td>
          <td>
            @can('detalhar_usuário')
            <a href="">Detalhar</a>
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-center mt-5">
    {{ $users->links() }}
  </div>
  {{-- <div class="">
    <div class="table-responsive">
      <x-table.table tableClass="border unded-3 w-100 table table-vcenter exclude table-hover card-table table-striped"
        tableId="dataTable">
        <x-slot:ths>
          <th>Nome</th>
          <th>Email</th>
          <th>Grupo</th>
          <th>Status</th>
          <th width="5%"></th>
          <th width="5%"></th>
        </x-slot:ths>
        <x-slot:trs>
          @foreach ($users as $user)
          <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td></td>
            <td>
              <x-badge.badge class="{{ $user->status == 1 ? 'bg-success' : 'bg-danger' }}">
                <x-slot:content>
                  {{ $user->status == 1 ? 'Ativo' : 'Inativo' }}
                </x-slot:content>
              </x-badge.badge>
            </td>
            <td>
              <button class="btn btn-secondary" data-bs-toggle="modal"
                data-bs-target="#modal-edit-user{{ $user->uuid }}"><i class="ti ti-edit"></i></button>
            </td>
            <td>
              <button class="btn btn-danger" data-bs-toggle="modal"
                data-bs-target="#modal-delete-user{{ $user->uuid }}"><i class="ti ti-trash"></i></button>
            </td>
          </tr>
          @endforeach
        </x-slot:trs>
      </x-table.table>
    </div>
  </div> --}}
</div>
@foreach ($users as $user)
<x-modal.modal route="{{ route('users.update', $user->uuid) }}" id="modal-edit-user{{ $user->uuid }}"
  class="modal-dialog-centered" title="Editar usuário" typeBtnClose="button" classBtnClose="me-auto"
  textBtnClose="Cancelar" typeBtnSave="submit" classBtnSave="btn-primary" textBtnSave="Salvar">
  <x-slot:content>
    @include('components.form-elements.input.input', [
    'title' => 'Nome',
    'type' => 'text',
    'class' => 'mb-3',
    'name' => 'name',
    'required' => 'true',
    'value' => $user->name,
    ])

    <x-form-elements.select.select title="Status" id="status" name="status">
      <x-slot:options>
        <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Ativo</option>
        <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inativo</option>
      </x-slot:options>
    </x-form-elements.select.select>

    {{-- <x-form-elements.select.select title="Grupo de permissões" id="role" name="role">
      <x-slot:options>
        @foreach ($roles as $role)
        <option value="{{ $role->name }}" {{ $role->name == $user->roles->first()->name ? 'selected' : '' }}>
          {{ $role->name }}
        </option>
        @endforeach
      </x-slot:options>
    </x-form-elements.select.select> --}}
  </x-slot:content>
</x-modal.modal>
<x-modal.modal-alert route="{{ route('users.destroy', $user->uuid) }}" id="modal-delete-user{{ $user->uuid }}"
  class="modal-dialog-centered modal-sm" background="bg-danger" classBody="text-center py-4" title="Excluír usuário"
  typeBtnClose="button" classBtnClose="me-auto w-100" textBtnClose="Cancelar" typeBtnSave="submit"
  classBtnSave="btn-danger w-100" textBtnSave="Deletar">
  <x-slot:content>
    <i class="ti ti-alert-triangle icon icon-lg text-danger"></i>
    <h3>Tem certeza?</h3>
    <div class="text-secondary">
      Você realmente deseja remover esse registro? Não será possível restaurá-lo depois!
    </div>
  </x-slot:content>
</x-modal.modal-alert>
@endforeach
@endsection
@section('scripts')
<script src="{{ asset('assets/js/kanban/startOneDataTable.js') }}"></script>
@endsection