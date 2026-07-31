@extends('templates.template')

@section('content')
<div class="container-xl">
  <div class="page-header d-print-none mb-4">
    <h2 class="page-title">Dashboard de Extensão</h2>
  </div>

  <div class="row row-cards">
    
    <div class="col-lg-5">
      <div class="row row-cards">

        <div class="col-lg-7">
            <div class="card" style="height: 100%; min-height: 600px;">
                <div class="card-header">
                <h3 class="card-title">Mapa de Atuação</h3>
                </div>
                <div class="card-body p-0">
                <div id="map" style="height: 100%; width: 100%; z-index: 1;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-primary text-white avatar"><i class="ti ti-briefcase"></i></span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">Em Andamento</div>
                  <div class="text-muted">{{ $totalAcoes }} ações ativas</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto">
                  <span class="bg-success text-white avatar"><i class="ti ti-currency-dollar"></i></span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">Bolsas Ativas</div>
                  <div class="text-muted">{{ $totalBolsas }} concedidas</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Ações por Área Temática</h3>
            </div>
            <div class="list-group list-group-flush">
              @forelse($acoesPorArea as $area)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  {{ $area->area_tematica ?? 'Não definida' }}
                  <span class="badge bg-blue-lt">{{ $area->total }}</span>
                </div>
              @empty
                <div class="list-group-item text-muted">Nenhum dado disponível.</div>
              @endforelse
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Eventos nos Próximos 7 Dias</h3>
            </div>
            <div class="list-group list-group-flush">
              @forelse($eventosDaSemana as $evento)
                <div class="list-group-item">
                  <div class="text-truncate">
                    <strong>{{ $evento->titulo_evento }}</strong>
                  </div>
                  <div class="text-muted small mt-1">
                    <i class="ti ti-calendar me-1"></i> 
                    {{ \Carbon\Carbon::parse($evento->data_hora_inicio)->format('d/m/Y \à\s H:i') }}
                    <br>
                    <i class="ti ti-map-pin me-1"></i> {{ $evento->local_formato }}
                  </div>
                  <div class="badge bg-secondary-lt mt-2 text-wrap text-start">
                    {{ $evento->acao->titulo ?? 'Ação não vinculada' }}
                  </div>
                </div>
              @empty
                <div class="list-group-item text-muted">Nenhum evento programado para os próximos dias.</div>
              @endforelse
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDetalhesAcao" aria-labelledby="offcanvasDetalhesAcaoLabel">
  <div class="offcanvas-header">
    <h2 class="offcanvas-title" id="offcanvasDetalhesAcaoLabel">Detalhes da Ação</h2>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div id="offcanvas-content">
      Selecione um ponto no mapa para ver os detalhes.
    </div>
  </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection