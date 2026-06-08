@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 mb-4">
    <div class="d-flex justify-content-end p-0 m-0">
      <x-table.search route="{{ route('actions.my') }}"></x-table.search>
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
  <div class="table-responsive p-0">
    <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
      <thead>
        <tr>
          <th class="text-wrap" style="min-width: 400px;">titulo</th>
          {{-- <th>id_atividade</th>
          <th>id_projeto</th> --}}
          {{-- <th>coordenador</th> --}}
          {{-- <th>centro_departamento</th> --}}
          <th>data_inicio</th>
          <th>data_fim</th>
          <th>ano</th>
          {{-- <th>tipo_acao</th>
          <th>area_tematica</th>
          <th>modalidade</th> --}}
          <th>status</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($actions as $item)
        <tr>
          <td class="text-wrap" style="min-width: 400px;">{{$item->action->titulo}}</td>
          {{-- <td>{{$item->action->id_atividade}}</td>
          <td>{{$item->action->id_projeto}}</td> --}}
          {{-- <td>{{$item->action->coordenador->name}}</td> --}}
          {{-- <td>{{$item->action->centro_departamento}}</td> --}}
          <td>{{date('d-m-Y', strtotime($item->action->data_inicio))}}</td>
          <td>{{date('d-m-Y', strtotime($item->action->data_fim))}}</td>
          <td>{{$item->action->ano}}</td>
          {{-- <td>{{$item->action->tipo_acao}}</td>
          <td>{{$item->action->area_tematica}}</td>
          <td>{{$item->action->modalidade}}</td> --}}
          <td>{{ $item->action->status == 0 ? 'Inativo' : ($item->action->status == 1 ? 'Ativo' : 'Finalizado') }}</td>
          <td class="text-center">
            @can('detalhar_ação')
            <a href="{{ route('actions.details', $item->action->id) }}" class="btn btn-sm">Detalhar</a>
            @endcan
          </td>
          <td class="text-center">
            <button class="btn btn-sm p-1 px-2" data-bs-toggle="offcanvas"
              data-bs-target="#modal-relatorios-{{$item->id}}" aria-controls="offcanvasExample">
              Relatórios
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{-- <div class="d-flex justify-content-center mt-5">
    {{ $actions->links() }}
  </div> --}}
  @foreach ($actions as $item)
  <x-modal.offcanvas id="modal-relatorios-{{$item->id}}" class="offcanvas-end"
    title="Relatórios para a ação: {{$item->titulo}}">
    <x-slot:content>
      <div class="list-group shadow-sm">
        @foreach ($item->action->submissoes as $submissao)
        <a href="{{ route('sessions.index', $submissao->id) }}" class="list-group-item list-group-item-action p-3">
          <div class="d-flex w-100 justify-content-between align-items-start gap-2 mb-2">
            <h6 class="fw-bold mb-0 text-dark text-break">
              {{ $submissao->relatorio->titulo }}
            </h6>
            <span class="badge rounded-pill flex-shrink-0 {{ $submissao->status == 0 ? 'text-bg-danger' : ($submissao->status == 1 ? 'text-bg-primary' : 'text-bg-success') }}">
              {{ $submissao->status == 0 ? 'Inativo' : ($submissao->status == 1 ? 'Ativo' : 'Finalizado') }}
            </span>
          </div>
          <div class="d-flex flex-column text-muted" style="font-size: 0.85rem;">
            <div class="mb-1">
              <span class="fw-semibold text-secondary">Início:</span>
              {{ date('d/m/Y H:i', strtotime($submissao->relatorio->data_inicio)) }}
            </div>
            <div class="mb-1">
              <span class="fw-semibold text-secondary">Prazo:</span>
              {{ date('d/m/Y H:i', strtotime($submissao->relatorio->prazo)) }}
            </div>
            <div>
              <span class="fw-semibold text-secondary">Finalizado:</span>
              <span class="{{ $submissao->finalizado_em == null ? 'text-danger' : 'text-success fw-bold' }}">
                {{ $submissao->finalizado_em == null ? 'Não' : 'Sim' }}
              </span>
            </div>
          </div>
        </a>
        @endforeach
      </div>
    </x-slot:content>
  </x-modal.offcanvas>
  @endforeach
</div>
@endsection
@section('scripts')
@endsection