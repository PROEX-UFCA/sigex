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
              <h3 class="card-title">Mapa atual da Extensão</h3>
              </div>
              <div class="card-body p-0">
              <div id="map" style="height: 100%; width: 100%; z-index: 1;"></div>
              </div>
          </div>
      </div>

    <div class="col-lg-5">
      <div class="row row-cards">
        
        <form method="GET" action="{{ route('dashboard.index') }}">
          <label for="anoFilter" class="font-bold mb-1">Filtrar por Ano</label>
          <select name="ano" id="anoFilter" class="form-select" onchange="this.form.submit()">
              @php
                  $anoAtual = date('Y');
                  $anoSelecionado = request('ano', $anoAtual);
              @endphp
              @for ($i = $anoAtual; $i >= $anoAtual - 5; $i--)
                  <option value="{{ $i }}" {{ $i == $anoSelecionado ? 'selected' : '' }}>
                      {{ $i }}
                  </option>
              @endfor
          </select>
        </form>

        <div class="col-12 mt-3">
          <div class="card">
            
            <ul class="nav nav-tabs" data-bs-toggle="tabs">
              <li class="nav-item">
                <a href="#tabs-resumo" class="nav-link active" data-bs-toggle="tab">Geral</a>
              </li>
              <li class="nav-item">
                <a href="#tabs-equipe" class="nav-link" data-bs-toggle="tab">Equipe</a>
              </li>
              <li class="nav-item">
                <a href="#tabs-areas" class="nav-link" data-bs-toggle="tab">Áreas</a>
              </li>
              <li class="nav-item">
                <a href="#tabs-dept" class="nav-link" data-bs-toggle="tab">Centros</a>
              </li>
              <li class="nav-item">
                <a href="#tabs-eventos" class="nav-link" data-bs-toggle="tab">Eventos</a>
              </li>
            </ul>

            <div class="card-body">
              <div class="tab-content">
                
            <!-- 1a tab: geral -->
                <div class="tab-pane active show mt-2" id="tabs-resumo">

                    <div class="row align-items-baseline text-center">

                        <div class="col">
                            <div class="justify-middle pe-lg-0">
                              <span class="bg-primary text-white avatar avatar-xs"><i class="ti ti-briefcase fs-3"></i></span>
                              <div class="font-weight-medium">{{ $totalAcoes }}</div>
                              <div class="text-muted fs-6">Ações Ativas</div>
                            </div>
                        </div>

                        <div class="col">
                              <div class="pe-lg-0">
                                  <span class="bg-success text-white avatar avatar-xs"><i class="ti ti-currency-dollar fs-3"></i></span>
                                  <div class="font-weight-medium">{{ $totalBolsas }}</div>
                                  <div class="text-muted fs-6">Bolsas Ativas</div>
                              </div>
                        </div>

                        <div class="col">
                            <span class="bg-info text-white avatar avatar-xs"><i class="ti ti-clock fs-3"></i></span>
                            <div class="font-weight-medium">{{ $duracaoMedia }} dias</div>
                            <div class="text-muted fs-6">Duração média das ações</div>
                        </div>

                    </div>

                  
                    
                    <!-- fluxo de cadastro, inicio e término de ações -->
                    <div class="col-12 mt-3 border-top">
                        <div class="my-3">
                            <h3 class="card-title mb-4 text-start">Fluxo Mensal de Ações</h3>
                            <div style="position: relative; height: 250px; width: 100%;">
                                <canvas id="fluxoAcoesChart"></canvas>
                            </div>
                        </div>
                    </div>
                
                    <!-- ods -->
                    <div class="col-12 mt-3 border-top">
                        <div class="my-3">
                            <h3 class="card-title mb-4 text-start   ">Quantidade de ações associadas a cada <span class="fw-bold">Objetivo de Desenvolvimento Sustentável</span>.</h3>
                            <div style="position: relative; height: 40dvh; width: 100%;">
                                <canvas id="odsChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
                
            <!-- 2a tab: equipe -->
                <div class="tab-pane" id="tabs-equipe">
                    <div class="row align-items-center">
                        <div class="text-center mb-3 mb-sm-0">
                            <div class="text-muted text-uppercase font-weight-bold" style="font-size: 0.8rem;">Total no ano</div>
                            <div class="display-4 font-weight-bold text-success">{{ $totalPessoas }}</div>
                            <div class="text-muted mt-1 mb-3">Pessoas Únicas</div>
                            <div style="position: relative; height: 200px; width: 100%;">
                                <canvas id="pessoasChart"></canvas>
                      </div>
                    </div>
                  </div>
                </div>

            <!-- 3a tab: areas -->
                <div class="tab-pane" id="tabs-areas">
                  <div style="position: relative; height: 300px; width: 100%;">
                      <canvas id="areaTematicaChart"></canvas>
                  </div>
                </div>

            <!-- 4a tab: centros -->
                <div class="tab-pane" id="tabs-dept">
                  <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="departamentoChart"></canvas>
                  </div>
                </div>

            <!-- 5a tab: eventos -->
                <div class="tab-pane" id="tabs-eventos">
                  <div class="row align-items-center">
                    <div class="col-sm-3 text-center mb-3 mb-sm-0">
                      <div class="text-muted text-uppercase font-weight-bold" style="font-size: 0.8rem;">Total anual</div>
                      <div class="display-4 font-weight-bold text-primary">{{ $totalEventos }}</div>
                      <div class="text-muted mt-1 fs-5">Eventos Realizados</div>
                    </div>
                    <div class="col-sm-9">
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
//MAPA
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
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    //GRÁFICO FLUXO MENSAL
        const dadosFluxo = @json($distribuicaoAcoes);
        
        const ctxFluxo = document.getElementById('fluxoAcoesChart').getContext('2d');
        new Chart(ctxFluxo, {
            type: 'line',
            data: {
                labels: dadosFluxo.map(item => item.mes),
                datasets: [
                    {
                        label: 'Cadastradas',
                        data: dadosFluxo.map(item => item.cadastradas),
                        borderColor: '#94a3b8',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Iniciadas',
                        data: dadosFluxo.map(item => item.iniciadas),
                        borderColor: '#10b981',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Finalizadas',
                        data: dadosFluxo.map(item => item.finalizadas),
                        borderColor: '#f43f5e',
                        borderWidth: 2,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

    //GRAFICO ODS
        const dadosOds = @json($todasOds);
        
        const labelsOds = Object.keys(dadosOds).map(ods => 'ODS ' + ods);
        const valoresReaisOds = Object.values(dadosOds);
        const fatiasIguais = Array(17).fill(1);

        const coresOficiaisOds = [
            '#E5243B', '#DDA63A', '#4C9F38', '#C5192D', '#FF3A21', '#26BDE2', '#FCC30B', 
            '#A21942', '#FD6925', '#DD1367', '#FD9D24', '#BF8B2E', '#3F7E44', '#0A97D9', 
            '#56C02B', '#00689D', '#19486A'
        ];

        const iconesPreCarregados = {};
        let imagensCarregadas = 0;

        for (let i = 1; i <= 17; i++) {
            iconesPreCarregados[i] = new Image();
            iconesPreCarregados[i].src = '{{ asset('assets/img/ods') }}/' + i + '.png'; 
            
            iconesPreCarregados[i].onload = function() {
                imagensCarregadas++;
                if (window.meuGraficoOds) {
                    window.meuGraficoOds.update();
                }
            };
        }

        const nomesOdsPT = [
            'Erradicação da Pobreza', 'Fome Zero', 'Saúde e Bem-Estar', 'Educação de Qualidade',
            'Igualdade de Gênero', 'Água Potável e Saneamento', 'Energia Limpa e Acessível',
            'Trabalho Decente e Crescimento Econômico', 'Indústria, Inovação e Infraestrutura',
            'Redução das Desigualdades', 'Cidades e Comunidades Sustentáveis',
            'Consumo e Produção Responsáveis', 'Ação Contra a Mudança Global do Clima',
            'Vida na Água', 'Vida Terrestre', 'Paz, Justiça e Instituições Eficazes',
            'Parcerias e Meios de Implementação'
        ];

        const unWheelPlugin = {
            id: 'unWheel',
            afterDatasetsDraw: function(chart) {
                const ctx = chart.ctx;
                const meta = chart.getDatasetMeta(0);
                
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                
                meta.data.forEach((arc, index) => {
                    const odsNumero = index + 1; 
                    const valorReal = valoresReaisOds[index];
                    const angulo = (arc.startAngle + arc.endAngle) / 2;
                    
                    // nº de ações)
                    const outerRadius = arc.outerRadius;
                    const outX = arc.x + Math.cos(angulo) * (outerRadius + 25);
                    const outY = arc.y + Math.sin(angulo) * (outerRadius + 25);
                    
                    ctx.fillStyle = coresOficiaisOds[index]; 
                    ctx.font = 'bold 16px sans-serif';
                    ctx.fillText(valorReal, outX, outY);

                    // 2. png
                    const innerRadius = arc.innerRadius;
                    const midRadius = innerRadius + ((outerRadius - innerRadius) / 2);
                    
                    const inX = arc.x + Math.cos(angulo) * midRadius;
                    const inY = arc.y + Math.sin(angulo) * midRadius;
                
                    
                    const icone = iconesPreCarregados[odsNumero];
                    if (icone.complete && icone.naturalHeight !== 0) {
                        ctx.save();
                        ctx.filter = 'brightness(0) invert(1)'; 
                        ctx.drawImage(icone, inX - 14, inY - 15, 25, 25); 
                        ctx.restore();
                    }
                });
                
                ctx.restore();
            }
        };

        const ctxOds = document.getElementById('odsChart').getContext('2d');

        window.meuGraficoOds = new Chart(ctxOds, {
            type: 'doughnut',
            data: {
                labels: labelsOds,
                datasets: [{
                    data: fatiasIguais,
                    backgroundColor: coresOficiaisOds,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            plugins: [unWheelPlugin], 
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '45%', 
                layout: { padding: 40 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const nome = nomesOdsPT[index];
                                const valor = valoresReaisOds[index];
                                return ` ${nome}: ${valor} ações`;
                            }
                        }
                    }
                }
            }
        });

    //GRÁFICO DE EQUIPE
        const dadosPessoas = @json($pessoasPorTipo);
        const labelsPessoas = dadosPessoas.map(item => item.tipo_membro || 'NÃO INFORMADO');
        const dataPessoas = dadosPessoas.map(item => item.total);

        const colorMapPessoas = {
            'discente': '#2563eb',
            'docente': '#16a34a',
            'servidor': '#d97706',
            'externo': '#9333ea'
        };

        const bgColorsPessoas = labelsPessoas.map(label => {
            const normal = label.toLowerCase().trim();
            return colorMapPessoas[normal] || '#cbd5e1';
        });

        const ctxPessoas = document.getElementById('pessoasChart').getContext('2d');

        new Chart(ctxPessoas, { 
            type: 'bar',
            data: {
                labels: labelsPessoas,
                datasets: [{
                    label: 'Pessoas Envolvidas',
                    data: dataPessoas,
                    backgroundColor: bgColorsPessoas,
                    borderRadius: 4,
                    borderWidth: 0
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
                                return context.raw + ' pessoas';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        },
                        grid: {
                            borderDash: [4, 4]
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    //GRÁFICO ÁREAS TEMÁTICAS
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

    //GRÁFICO DE CENTROS/DEPARTAMENTOS
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
        
    //GRÁFICO DE EVENTOS

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
</script>
@endsection