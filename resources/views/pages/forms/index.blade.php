@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 mb-4 row">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      <bottom class="btn" data-bs-toggle="collapse" data-bs-target="#inserir" aria-expanded="false"
        aria-controls="collapseExample">Inserir</bottom>
    </div>
    <div class="d-flex justify-content-end col-12 col-md-6 p-0 m-0">
      <x-table.search route="{{ route('forms.index') }}"></x-table.search>
    </div>
  </div>
  <div class="collapse m-0 p-0 mb-3" id="inserir">
    <div class="card card-body m-0 p-3">
      <form action="{{ route('forms.store') }}" method="POST" class="row">
        @csrf
        @include('components.form-elements.input.input', [
        'title' => 'Título',
        'type' => 'text',
        'class' => 'mb-3 col-12 col-md-4',
        'name' => 'titulo',
        'required' => 'true',
        'placeholder' => 'Insira um título para o formulário',
        'value' => request('titulo')
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Qtd. de Seções',
        'type' => 'number',
        'class' => 'mb-3 col-12 col-md-2',
        'name' => 'numero_secoes',
        'min' => 1,
        'required' => 'true',
        'placeholder' => 'Insira a quantidade de seções do formulário',
        'value' => request('numero_secoes')
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Descrição',
        'type' => 'text',
        'class' => 'mb-3 col-12 col-md-4',
        'name' => 'descricao',
        'required' => 'true',
        'placeholder' => 'Insira uma descrição para o formulário',
        'value' => request('descricao')
        ])


        <div class="col-12 col-md-2">
          <label class="form-label d-block">&nbsp;</label>
          <div class="d-flex">
            <button class="btn btn-green w-100" type="submit">Salvar</button>
          </div>
        </div>

      </form>
    </div>
  </div>
  <div class="table-responsive p-0">
    <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
      <thead>
        <tr>
          @php
          $sortField = request('sort', 'created_at');
          $sortDirection = request('dir', 'desc');
          $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';
          @endphp

          @foreach ([
          'titulo' => 'Título',
          'descricao' => 'Descrição',
          'status' => 'Status',
          'created_at' => 'Data de Criação',
          ] as $field => $label)
          <th>
            <a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'dir' => $sortField === $field ? $nextDirection : 'asc']) }}"
              class="text-reset text-decoration-none d-flex align-items-center gap-1"
              style="{{ $label === 'Título' ? 'min-width: 200px;' : '' }}"
              class="{{ $label === 'Título' ? 'text-wrap' : '' }}">
              {{ $label }}
              @if ($sortField === $field)
              {{ $sortDirection === 'asc' ? '↑' : '↓' }}
              @endif
            </a>
          </th>
          @endforeach
          <th width="5%"></th>
          <th width="5%"></th>
          <th width="5%"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($forms as $item)
        <tr>
          <td class="text-wrap" style="min-width: 200px;">{{$item->titulo}}</td>
          <td>{{$item->descricao}}</td>
          <td>{{ $item->status == 0 ? 'Inativo' : ($item->status == 1 ? 'Ativo' : 'Finalizado') }}</td>
          <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
          <td class="text-center">
            <button class="btn btn-sm p-1 px-2" data-bs-toggle="offcanvas" data-bs-target="#modal-config-{{$item->id}}"
              aria-controls="offcanvasExample">
              Seções
            </button>
          </td>
          <td class="text-center">
            <button class="btn btn-sm p-1 px-2" data-bs-toggle="offcanvas" data-bs-target="#modal-edit-{{$item->id}}"
              aria-controls="offcanvasExample">
              Editar
            </button>
          </td>
          <td class="text-center">
            <form action="{{ route('forms.destroy', $item->id) }}" method="POST"
              onsubmit="return confirm('Deseja realmente deletar este formulário?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger p-1 px-2 {{ $item->published == 0 ? '' : 'disabled' }}" type="submit">
                Deletar
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-center mt-5">
    {{ $forms->links() }}
  </div>
  @foreach ($forms as $item)
  <x-modal.offcanvas id="modal-config-{{$item->id}}" class="offcanvas-end"
    title="Seções do formulário {{$item->titulo}}">
    <x-slot:content>
      <ol class="list-group list-group-numbered">
        @foreach ($item->secoes as $secao)
        <a href="{{route('sessions.index', $secao->id)}}"
          class="text-decoration-none list-group-item d-flex justify-content-between align-items-start">
          <div class="ms-2 me-auto">
            <div class="fw-bold">{{$secao->titulo}}</div>
            {{$secao->descricao}}
          </div>
          <span class="badge rounded-pill {{$secao->perguntas->count() == 0 ? 'text-bg-danger' : 'text-bg-primary'}}">
            {{$secao->perguntas->count()}} Perguntas
          </span>
        </a>
        @endforeach

        @if($item->published == 0)
        <button class="btn btn-azure  mt-3" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#modal-add-{{$item->id}}" aria-controls="offcanvasExample">Adicionar seção</button>
        @else
        <div class="alert alert-danger mt-3">
          Não é possível mais adicionar seções pois o formulário já foi publicado
        </div>
        @endif
      </ol>
    </x-slot:content>
  </x-modal.offcanvas>

  <x-modal.offcanvas route="{{ route('forms.update', $item->id) }}" id="modal-edit-{{$item->id}}" class="offcanvas-end"
    title="Editar formulário">
    <x-slot:content>
      @include('components.form-elements.input.input', [
      'title' => 'Título',
      'type' => 'text',
      'class' => 'mb-3 col-12',
      'name' => 'titulo',
      'required' => 'true',
      'placeholder' => 'Insira um título para o formulário',
      'value' => $item->titulo
      ])

      @include('components.form-elements.textarea.textarea', [
      'title' => 'Descrição',
      'class' => 'mb-3 col-12',
      'name' => 'descricao',
      'required' => 'true',
      'placeholder' => 'Insira uma descrição para o formulário',
      'value' => $item->descricao
      ])
      <x-form-elements.select.select title="Status" id="status" name="status" class="col-12" required="true">
        <x-slot:options>
          <option value="" disabled>Selecione</option>
          <option value="0" {{ $item->status =='0' ? 'selected' : '' }}>Inativo</option>
          <option value="1" {{ $item->status =='1' ? 'selected' : '' }}>Ativo</option>
        </x-slot:options>
      </x-form-elements.select.select>

      @if ($item->published == 0)
      <div class="alert alert-danger">
        Ao mudar o status do formulário para ativo pela primeira vez não será mais possível mudar ou editar suas seções
        e perguntas.
      </div>
      @endif
    </x-slot:content>
  </x-modal.offcanvas>

  <x-modal.offcanvas route="{{ route('sessions.store', $item->id) }}" id="modal-add-{{$item->id}}" class="offcanvas-end"
    title="Adicionar seção">
    <x-slot:content>
      @include('components.form-elements.input.input', [
      'title' => 'Título',
      'type' => 'text',
      'class' => 'mb-3 col-12',
      'name' => 'titulo',
      'required' => 'true',
      'placeholder' => 'Insira um título para a seção',
      'value' => ''
      ])

      @include('components.form-elements.textarea.textarea', [
      'title' => 'Descrição',
      'class' => 'mb-3 col-12',
      'name' => 'descricao',
      'required' => 'true',
      'placeholder' => 'Insira uma descrição para a seção',
      'value' => ''
      ])
    </x-slot:content>
  </x-modal.offcanvas>
  @endforeach
</div>
@endsection
@section('scripts')
@endsection