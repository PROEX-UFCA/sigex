@extends('templates.template')

@section('content')
<div class="container-xl">
  <div class="page-header d-print-none mb-4">
    <h2 class="page-title">Dashboard das Ações de Extensão</h2>
  </div>

  <div class="row row-cards">
    
      <div class="col-lg-7">
          <div class="card" style="height: calc(100vh - 100px); min-height: 500px; position: sticky; top: 20px;">
              <div class="card-header">
              <h3 class="card-title">Mapa de Atuação</h3>
              </div>
              <div class="card-body p-0">
              <div id="map" style="height: 100%; width: 100%; z-index: 1;"></div>
              </div>
          </div>
      </div>

    <div class="col-lg-5">
      <div class="row row-cards">

        
        <div class="col-sm-6">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto pe-lg-0">
                  <span class="bg-primary text-white avatar avatar-xs"><i class="ti ti-briefcase fs-3"></i></span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">Em Andamento</div>
                  <div class="text-muted fs-6">{{ $totalAcoes }} ações ativas</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto pe-lg-0">
                  <span class="bg-success text-white avatar avatar-xs"><i class="ti ti-currency-dollar fs-3"></i></span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">Bolsas Ativas</div>
                  <div class="text-muted fs-6">{{ $totalBolsas }} concedidas</div>
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
                <div class="list-group-item text-muted" style="max-height: 350px; overflow-y: auto;">Nenhum dado disponível.</div>
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
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var map = L.map('map').setView([-7.3, -39.3], 9);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // receber os dados brutos
    var locais = {!! $pontosMapa !!};
    var marcadoresParaEnquadrar = [];

    // lógica dos pins
    locais.forEach(function(local) {
        if(local.lat && local.lng) {
            var marker = L.marker([local.lat, local.lng]).addTo(map);
            marker.bindTooltip(
                "<div style='white-space: normal; max-width: 220px; text-align: center; line-height: 1.4;'>" +
                "<strong>" + local.acao_titulo + "</strong><br>" +
                "<small class='text-muted'><em>\"" + local.pergunta_texto + "\"</em></small>" +
                "</div>", 
                { direction: 'top', offset: [0, -15], opacity: 0.95 }
            );

            marker.on('click', function() {
                abrirOffcanvas(local);
            });
              
            marcadoresParaEnquadrar.push([local.lat, local.lng]);
        }
    });

    if (marcadoresParaEnquadrar.length > 0) {
        var limites = L.latLngBounds(marcadoresParaEnquadrar);
        
        map.fitBounds(limites, { padding: [50, 50] }); 
    }

    function abrirOffcanvas(dados) {
        var tituloEl = document.getElementById('offcanvasDetalhesAcaoLabel');
        var conteudoEl = document.getElementById('offcanvas-content');

        tituloEl.innerText = dados.acao_titulo;

        var html = '';
        
        if(dados.acao_img) {
            html += '<img src="/storage/' + dados.acao_img + '" class="img-fluid rounded mb-3 w-100" style="object-fit: cover; max-height: 200px;" alt="Imagem da Ação">';
        }

        html += '<div class="mb-3 p-3 bg-light rounded border">';
        html += '  <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Contexto do Local</small>';
        html += '  <p class="mb-0 mt-1"><strong>' + dados.pergunta_texto + '</strong></p>';
        html += '</div>';

        html += '<div class="mb-3">';
        html += '  <h4 class="mb-1"><i class="ti ti-map-pin text-muted me-2"></i>Local indicado</h4>';
        html += '  <p class="text-muted">' + dados.nome_local + '</p>';
        html += '</div>';

        html += '<div class="mb-3">';
        html += '  <h4 class="mb-1"><i class="ti ti-file-description text-muted me-2"></i>Resumo</h4>';
        html += '  <p class="text-muted">' + (dados.acao_resumo ? dados.acao_resumo : 'Nenhum resumo disponível para esta ação.') + '</p>';
        html += '</div>';

        // link pra página da ação, 
        // pensando bem: talvez seja bom retirar já que precisa de permissão pra detalhar ação mas essa seção de dashboard não exige permissão nenhuma. ver com otávio dps
        html += '<div class="mt-4">';
        html += '  <a href="/system/actions/' + dados.acao_id + '/details" class="btn btn-primary w-100">';
        html += '    Ver Detalhes Completos';
        html += '  </a>';
        html += '</div>';

        conteudoEl.innerHTML = html;

        var offcanvasElement = document.getElementById('offcanvasDetalhesAcao');
        var bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
        if (!bsOffcanvas) {
            bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        }
        bsOffcanvas.show();
    }
});
</script>
@endsection