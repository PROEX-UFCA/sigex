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
</div>
<div class="table-responsive p-0">
  <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
    <thead>
      <tr>
        <th class="text-wrap">titulo</th>
        {{-- <th>vínculo</th> --}}
        <th>inicio</th>
        <th>fim</th>
        <th>situação</th>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($actions as $item)
      <tr>
        <td class="text-wrap" style="min-width: 400px;">{{$item->action->titulo}}</td>
        {{-- <td>{{$item->categorias_membros}}</td> --}}
        <td>{{date('d/m/Y', strtotime($item->action->data_inicio))}}</td>
        <td>{{date('d/m/Y', strtotime($item->action->data_fim))}}</td>
        <td>{{ $item->action->situacao }}</td>
        <td class="text-center">
          @can('detalhar_ação')
          <a href="{{ route('actions.details', $item->action->id) }}" class="btn btn-sm">Detalhar</a>
          @endcan
        </td>
        <td class="text-center">
          @php
          $submissoesPendentes = $item->action->submissoes->filter(function($submissao) {
          return is_null($submissao->finalizada_em) || $submissao->evaluation_progress < 100; })->count();
            @endphp

            <button class="btn btn-sm p-1 px-2 position-relative" data-bs-toggle="offcanvas"
              data-bs-target="#modal-relatorios-{{$item->action->id}}" aria-controls="offcanvasExample">
              Relatórios

              @if ($submissoesPendentes > 0)
              <span class="badge bg-danger badge-notification">
                {{ $submissoesPendentes }}
              </span>
              @endif
            </button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="d-flex justify-content-center mt-5">
  {{ $actions->links() }}
</div>
@foreach ($actions as $item)
<x-modal.offcanvas id="modal-relatorios-{{$item->action->id}}" class="offcanvas-end"
  title="Relatórios para a ação: {{$item->action->titulo}}">
  <x-slot:content>
    <div class="list-group shadow-sm">
      @foreach ($item->action->submissoes as $submissao)
      <a href="{{ route('actions.report', $submissao->id) }}" class="list-group-item list-group-item-action p-3">
        <div class="d-flex w-100 justify-content-between align-items-start gap-2 mb-2">
          <h3 class="fw-bold mb-0 text-dark text-break">
            {{ $submissao->relatorio->titulo }}
          </h3>
          <span
            class="badge rounded-pill flex-shrink-0 {{ $submissao->relatorio->status == 0 ? 'text-bg-danger' : ($submissao->relatorio->status == 1 ? 'text-bg-primary' : 'text-bg-success') }}">
            {{ $submissao->relatorio->status == 0 ? 'Inativo' : ($submissao->relatorio->status == 1 ? 'Ativo' :
            'Finalizado') }}
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
            <span class="{{ $submissao->finalizada_em == null ? 'text-danger' : 'text-success fw-bold' }}">
              {{ $submissao->finalizada_em == null ? 'Não' : date('d/m/Y H:i:s', strtotime($submissao->finalizada_em))
              }}
            </span>
          </div>

          <div class="mt-3">
            <div class="row g-2 align-items-center mb-2">
              <div class="col-12 mb-0 pb-0" style="font-size: 0.75rem;">Progresso de Preenchimento</div>
              <div class="col-auto fw-bold">{{$submissao->progress}}%</div>
              <div class="col">
                <div class="progress progress-sm">
                  <div class="progress-bar bg-primary" style="width: {{$submissao->progress}}%" role="progressbar"
                    aria-valuenow="{{$submissao->progress}}" aria-valuemin="0" aria-valuemax="100">
                  </div>
                </div>
              </div>
            </div>

            @if(!is_null($submissao->finalizada_em))
            <div class="row g-2 align-items-center">
              <div class="col-12 mb-0 pb-0" style="font-size: 0.75rem;">Progresso da Avaliação</div>
              <div class="col-auto fw-bold">{{$submissao->evaluation_progress}}%</div>
              <div class="col">
                <div class="progress progress-sm">
                  <div class="progress-bar {{ $submissao->evaluation_progress == 100 ? 'bg-success' : 'bg-warning' }}"
                    style="width: {{$submissao->evaluation_progress}}%" role="progressbar"
                    aria-valuenow="{{$submissao->evaluation_progress}}" aria-valuemin="0" aria-valuemax="100">
                  </div>
                </div>
              </div>
            </div>

            @if($submissao->evaluation_progress < 100) <div
              class="mt-2 text-danger fw-bold d-flex align-items-center gap-1" style="font-size: 0.8rem;">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                <path
                  d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
              </svg>
              Requer correções! Clique para ajustar.
          </div>
          @endif
          @endif

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