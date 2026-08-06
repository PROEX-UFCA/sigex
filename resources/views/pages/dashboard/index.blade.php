@extends('templates.template')

@section('content')
<div class="container-xl">
  <div class="page-header d-print-none mb-4">
    <h2 class="page-title">Painel das Ações de Extensão</h2>
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
        
        <form method="GET" action="{{ route('dashboard.index') }}">
          <label for="anoFilter" class="font-bold mb-1">Filtrar por Ano de Execução:</label>
          
          <select name="anos[]" id="anoFilter" class="form-select" onchange="this.form.submit()">
              @php
                  $anoAtual = date('Y');
                  $anosSelecionados = request('anos', [$anoAtual]);
              @endphp
              @for ($i = $anoAtual; $i >= $anoAtual - 5; $i--)
                  <option value="{{ $i }}" {{ in_array($i, $anosSelecionados) ? 'selected' : '' }}>
                      {{ $i }}
                  </option>
              @endfor
          </select>

        </form>
        
        <div class="col-sm-6">
          <div class="card card-sm">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-auto pe-lg-0">
                  <span class="bg-primary text-white avatar avatar-xs"><i class="ti ti-briefcase fs-3"></i></span>
                </div>
                <div class="col">
                  <div class="font-weight-medium">{{ $totalAcoes }}</div>
                  <div class="text-muted fs-6">Ações Ativas</div>
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
                  <div class="font-weight-medium">{{ $totalBolsas }}</div>
                  <div class="text-muted fs-6">Bolsas Ativas</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card bg-white p-4 ">
            <h3 class="text-lg font-bold mb-4">Ações por Área Temática</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="areaTematicaChart"></canvas>
            </div>
        </div>
        </div>

        <div class="col-12 mt-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Ações por Departamento</h3>
            </div>
            <div class="card-body">
              <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="departamentoChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 mt-3">
          <div class="card">
            <div class="card-body">
              <h3 class="card-title mb-4">Frequência de Eventos</h3>
              
              <div class="row align-items-center">

                <div class="col-sm-4 text-center mb-3 mb-sm-0">
                  <div class="text-muted text-uppercase font-weight-bold" style="font-size: 0.8rem;">Total anual</div>
                  <div class="display-4 font-weight-bold text-primary">{{ $totalEventos }}</div>
                  <div class="text-muted mt-1">Eventos Realizados</div>
                </div>

                <div class="col-sm-8">
                  <div style="position: relative; height: 180px; width: 100%;">
                    <canvas id="eventosChart"></canvas>
                  </div>
                </div>

              </div>

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
        html += '  <p class="text-muted">' + (dados.acao_resumo ? dados.acao_resumo : 'Nenhum resumo disponível para esta ação.') + '</p>';
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dadosArea = @json($acoesPorArea);

        const labels = dadosArea.map(item => item.area_tematica);
        const dataValues = dadosArea.map(item => item.total);

        const totalAcoes = dataValues.reduce((a, b) => a + b, 0);

        const colorMap = {
            'saúde': '#8b5cf6',
            'educação': '#f97316',
            'meio ambiente': '#10b981',
            'tecnologia e produção': '#06b6d4',
            'trabalho': '#92400e',
            'comunicação': '#ec4899',
            'cultura': '#eab308',
            'direitos humanos e justiça': '#64748b'
        };

        const backgroundColors = labels.map(label => {
            const normalizedLabel = label.toLowerCase().trim();
            return colorMap[normalizedLabel] || '#cbd5e1'; 
        });

        const centerTextPlugin = {
            id: 'centerText',
            beforeDraw: function(chart) {
                if (chart.config.type !== 'doughnut') return;

                const ctx = chart.ctx;
                
                const meta = chart.getDatasetMeta(0);
                if (!meta || !meta.data || meta.data.length === 0) return;
                
                const centerX = meta.data[0].x;
                const centerY = meta.data[0].y;

                let visibleTotal = 0;
                chart.data.datasets[0].data.forEach((value, index) => {
                    if (chart.getDataVisibility(index)) {
                        visibleTotal += value;
                    }
                });

                ctx.save();
                
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                
                const fontSize = (chart.height / 120).toFixed(2);
                ctx.font = "bold " + fontSize + "em sans-serif";
                ctx.fillStyle = "#1e293b"; 

                ctx.fillText(visibleTotal.toString(), centerX, centerY - (chart.height * 0.03));

                const labelFontSize = (chart.height / 300).toFixed(2);
                ctx.font = labelFontSize + "em sans-serif";
                ctx.fillStyle = "#64748b"; 

                ctx.fillText("Ações", centerX, centerY + (chart.height * 0.09));
                
                ctx.restore();
            }
        };

        const ctx = document.getElementById('areaTematicaChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            plugins: [centerTextPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                return ' ' + context.raw + ' ações';
                            }
                        }
                    }
                }
            }
        });

        // GRÁFICOS DE CENTROS
        const dadosDept = @json($acoesPorDepartamento);

        const labelsCentros = dadosDept.map(item => item.departamento_grupo);
        const dataCentros = dadosDept.map(item => item.total);

        const colorMapDept = {
            'ccab': '#8b5cf6',
            'ccsa': '#f97316',
            'cct': '#10b981',
            'famed': '#06b6d4',
            'ife': '#ec4899',
            'iisca': '#eab308',
            'administrativo': '#94a3b8'
        };

        const centrosColors = labelsCentros.map(label => {
            const normalizedLabel = label.toLowerCase().trim();
            return colorMapDept[normalizedLabel] || '#cbd5e1';
        });

        const ctxDept = document.getElementById('departamentoChart').getContext('2d'); 
        
        new Chart(ctxDept, {
            type: 'bar',
            data: {
                labels: labelsCentros,
                datasets: [{
                    label: 'Ações',
                    data: dataCentros,
                    backgroundColor: centrosColors,
                    borderRadius: 4,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    },
                    y: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.raw + ' ações';
                            }
                        }
                    }
                }
            }
        });
        
        //GRÁFICOS DE EVENTOS

        const dadosEventos = @json($distribuicaoMensal);
        const labelsEventos = dadosEventos.map(item => item.mes);
        const dataEventos = dadosEventos.map(item => item.total);

        const ctxEventos = document.getElementById('eventosChart').getContext('2d');

        new Chart(ctxEventos, {
            type: 'line',
            data: {
                labels: labelsEventos,
                datasets: [{
                    label: 'Eventos',
                    data: dataEventos,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    borderWidth: 2,
                    tension: 0.4, //
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false 
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' eventos';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            borderDash: [4, 4]
                        }
                    },
                    x: {
                        grid: {
                        }
                    }
                }
            }
        });
    });
</script>
@endsection