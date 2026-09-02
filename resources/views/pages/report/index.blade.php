@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 mb-4 row">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      <a href="{{route('actions.index')}}" class="btn">Voltar</a>
      @can('adicionar_relatórios')
      <a href="{{ route('report.create') }}" class="btn">Novo relatório</a>
      @endcan
    </div>
    <div class="d-flex justify-content-end col-12 col-md-6 p-0 m-0">
      <x-table.search route="{{ route('report.index') }}"></x-table.search>
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
          'data_inicio' => 'Data início',
          'prazo' => 'Prazo',
          'status' => 'Status',
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
          <th width="5%">Qtd. Ações</th>
          @can('editar_relatórios')
          <th width="5%"></th>
          @endcan
          @can('monitorar_relatórios')
          <th width="5%"></th>
          @endcan
        </tr>
      </thead>
      <tbody>
        @foreach ($reports as $item)
        <tr>
          <td class="text-wrap" style="min-width: 200px;">{{$item->titulo}}</td>
          <td>{{date('d/m/Y H:i', strtotime($item->data_inicio))}}</td>
          <td>{{date('d/m/Y H:i', strtotime($item->prazo))}}</td>
          <td>{{ $item->status == 0 ? 'Inativo' : ($item->status == 1 ? 'Ativo' : 'Finalizado') }}</td>
          <td>{{$item->submissoes->count()}}</td>
          @can('editar_relatórios')
          <td class="text-center">
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
              <button class="btn btn-sm btn-primary btn-icon" data-bs-toggle="offcanvas" data-bs-target="#modal-edit-{{$item->id}}" aria-controls="offcanvasExample">
                <i class="ti ti-pencil"></i>
              </button>
            </span>
          </td>
          
          @endcan
          @can('monitorar_relatórios')
          <td class="text-center">
            <a href="{{route('report.monitor', $item->id)}}" class="btn btn-sm btn-success">Homologar</a>
          </td>
          @endcan
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-center mt-5">
    {{ $reports->links() }}
  </div>
  @can('editar_relatórios')
  @foreach ($reports as $item)
  <x-modal.offcanvas route="{{ route('report.update', $item->id) }}" id="modal-edit-{{$item->id}}" class="offcanvas-end"
    title="Editar relatório">
    <x-slot:content>


      @include('components.form-elements.input.input', [
      'title' => 'Título do relatório',
      'type' => 'text',
      'class' => 'mb-3 col-12',
      'name' => 'titulo',
      'required' => 'true',
      'placeholder' => 'Digite o nome do relatório.',
      'value' => $item->titulo ?? '',
      ])

      @include('components.form-elements.input.input', [
      'title' => 'Data de início',
      'type' => 'datetime-local',
      'class' => 'mb-3 col-12',
      'name' => 'data_inicio',
      'required' => 'true',
      'value' => $item->data_inicio ?? '',
      ])

      @include('components.form-elements.input.input', [
      'title' => 'Prazo',
      'type' => 'datetime-local',
      'class' => 'mb-3 col-12',
      'name' => 'prazo',
      'required' => 'true',
      'value' => $item->prazo ?? '',
      ])

      <x-form-elements.select.select title="Status" id="status" name="status" class="col-12" required="true">
        <x-slot:options>
          <option value="" disabled>Selecione</option>
          <option value="0" {{ $item->status =='0' ? 'selected' : '' }}>Inativo</option>
          <option value="1" {{ $item->status =='1' ? 'selected' : '' }}>Ativo</option>
          <option value="2" {{ $item->status =='2' ? 'selected' : '' }}>Finalizado</option>
        </x-slot:options>
      </x-form-elements.select.select>

    </x-slot:content>
  </x-modal.offcanvas>
  @endforeach
  @endcan
</div>
@endsection
@section('scripts')
@endsection