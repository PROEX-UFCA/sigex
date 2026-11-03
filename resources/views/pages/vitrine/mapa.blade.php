@extends('pages.vitrine.template')
@section('styles')
<style>
    .tooltip {
        --bs-tooltip-bg: var(--tblr-brown, #532B1D);
        --bs-tooltip-color: var(--tblr-yellow, #F5BE56);
        --bs-tooltip-font-family: var(--tblr-font-sans-serif, 'Alegreya', serif);
        --bs-tooltip-font-size: 0.9rem;
        --bs-tooltip-border-radius: 6px;
        --bs-tooltip-padding-x: 10px;
        --bs-tooltip-padding-y: 6px;
    }

    .municipio {
        transition: fill 0.2s ease, stroke-width 0.2s ease, filter 0.2s ease;
        cursor: pointer;
        outline: none;
    }

    .municipio:hover {
        filter: brightness(0.95);
        stroke-width: 2;
    }

    .municipio.cidade-ativa {
        fill: var(--tblr-yellow, #F5BE56); 
        stroke: var(--tblr-brown, #532B1D);
        stroke-width: 1.5px;
    }

    .municipio.cidade-ativa:hover {
        filter: brightness(0.9);
    }

    .map-responsive {
        width: 90%;
    }

    @media (min-width: 768px) {
        .map-responsive {
            width: 70%;
        }
    }

    @media (max-width: 1000px) {
        #panel-filtros .card-title {
            font-size: 13px !important; 
        }
        
        #panel-filtros .form-select {
            font-size: 13px;
            padding-top: 13px;
            padding-bottom: 13px;
        }
    }
</style>
@endsection

@section('content')
<div class="row g-4 mt-2">
    <div class="col-12 col-lg-9">
        <div class="card shadow-sm">
            <div class="map-container map-responsive mx-auto p-4">
                <x-mapas.mapa-regiao />
                
                <div class="mt-4 pt-4 border-top d-flex flex-wrap justify-content-center gap-3 gap-md-4" style="font-size: 0.85rem;">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 18px; height: 18px; background-color: var(--tblr-yellow, #F5BE56); border: 1.5px solid var(--tblr-brown, #532B1D); border-radius: 4px;"></span>
                        <span class="fw-bold text-dark">Cidades com ações</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 16px; height: 16px; background-color: #f8cab4; border-radius: 4px; border: 1px solid #dee2e6;"></span>
                        <span class="text-muted">Cariri</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 16px; height: 16px; background-color: #d3e8c4; border-radius: 4px; border: 1px solid #dee2e6;"></span>
                        <span class="text-muted">Centro-Sul</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 16px; height: 16px; background-color: #eacec3; border-radius: 4px; border: 1px solid #dee2e6;"></span>
                        <span class="text-muted">Vale dos Inhamuns</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 16px; height: 16px; background-color: #fbe1b1; border-radius: 4px; border: 1px solid #dee2e6;"></span>
                        <span class="text-muted">Sertão Central</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 16px; height: 16px; background-color: #c0e9ec; border-radius: 4px; border: 1px solid #dee2e6;"></span>
                        <span class="text-muted">Vale do Jaguaribe</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 16px; height: 16px; background-color: #c9f0e0; border-radius: 4px; border: 1px solid #dee2e6;"></span>
                        <span class="text-muted">Pernambuco</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 16px; height: 16px; background-color: #f8fafc; border-radius: 4px; border: 1px solid #dee2e6;"></span>
                        <span class="text-muted">Outras regiões</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-12 col-lg-3">
        
        <div id="panel-filtros" class="card shadow-sm sticky-top" style="top: 2rem;">
            <div class="card-header bg-light">
                <h3 class="card-title text-brown m-0 fw-bold"><i class="ti ti-filter me-2"></i>Filtros do Mapa</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('vitrine.mapa') }}" method="GET">
                    @php
                        $situacaoAtual = request()->has('situacao') ? request('situacao') : 'EM EXECUÇÃO'; 
                    @endphp

                    <label class="form-label fw-bold text-muted small">Situação</label>
                    <select name="situacao" class="form-select mb-3">
                        <option value="EM EXECUÇÃO" {{ $situacaoAtual == 'EM EXECUÇÃO' ? 'selected' : '' }}>Em Execução</option>
                        <option value="CONCLUÍDA" {{ $situacaoAtual == 'CONCLUÍDA' ? 'selected' : '' }}>Concluída</option>
                        <option value="TODAS" {{ $situacaoAtual == 'TODAS' ? 'selected' : '' }}>Todas as situações</option>
                    </select>
                    
                    <label class="form-label fw-bold text-muted small">Área Temática</label>
                    <select name="area_tematica" id="filter-tema" class="form-select mb-3">
                        <option value="">Todas as áreas temáticas</option>
                        <option value="Comunicação" {{ request('area_tematica') == 'Comunicação' ? 'selected' : '' }}>Comunicação</option>
                        <option value="Cultura" {{ request('area_tematica') == 'Cultura' ? 'selected' : '' }}>Cultura</option>
                        <option value="Direitos Humanos e Justiça" {{ request('area_tematica') == 'Direitos Humanos e Justiça' ? 'selected' : '' }}>Direitos Humanos e Justiça</option>
                        <option value="Educação" {{ request('area_tematica') == 'Educação' ? 'selected' : '' }}>Educação</option>
                        <option value="Meio Ambiente" {{ request('area_tematica') == 'Meio Ambiente' ? 'selected' : '' }}>Meio Ambiente</option>
                        <option value="Saúde" {{ request('area_tematica') == 'Saúde' ? 'selected' : '' }}>Saúde</option>
                        <option value="Tecnologia e Produção" {{ request('area_tematica') == 'Tecnologia e Produção' ? 'selected' : '' }}>Tecnologia e Produção</option>
                        <option value="Trabalho" {{ request('area_tematica') == 'Trabalho' ? 'selected' : '' }}>Trabalho</option>
                    </select>
                    <label class="form-label fw-bold text-muted small">ODS</label>
                        <select name="ods" class="form-select mb-4">
                            <option value="">Todos os ODS</option>
                                @php
                                    $odsNomes = [
                                        1 => 'Erradicação da Pobreza',
                                        2 => 'Fome Zero e Agricultura Sustentável',
                                        3 => 'Saúde e Bem-Estar',
                                        4 => 'Educação de Qualidade',
                                        5 => 'Igualdade de Gênero',
                                        6 => 'Água Potável e Saneamento',
                                        7 => 'Energia Limpa e Acessível',
                                        8 => 'Trabalho Decente e Crescimento Econômico',
                                        9 => 'Indústria, Inovação e Infraestrutura',
                                        10 => 'Redução das Desigualdades',
                                        11 => 'Cidades e Comunidades Sustentáveis',
                                        12 => 'Consumo e Produção Responsáveis',
                                        13 => 'Ação Contra a Mudança Global do Clima',
                                        14 => 'Vida na Água',
                                        15 => 'Vida Terrestre',
                                        16 => 'Paz, Justiça e Instituições Eficazes',
                                        17 => 'Parcerias e Meios de Implementação',
                                    ];
                                @endphp
                                
                                @for($i = 1; $i <= 17; $i++)
                                    <option value="{{ $i }}" {{ request('ods') == (string)$i ? 'selected' : '' }}>
                                        ODS {{ $i }}: {{ $odsNomes[$i] }}
                                    </option>
                                @endfor
                        </select>

                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-brown w-100">
                            Aplicar Filtros
                        </button>
                        <a href="{{ route('vitrine.mapa') }}" class="btn btn-outline-warning w-100">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div id="panel-insights" class="card shadow-sm sticky-top d-none" style="top: 2rem;">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 id="insight-cidade-nome" class="card-title text-brown m-0 fw-bold">Nome da Cidade</h3>
                <button id="btn-voltar-filtros" class="btn btn-sm btn-outline-secondary border-0">
                    <i class="ti ti-x fs-3"></i>
                </button>
            </div>
            <div class="card-body text-center">
                <h1 id="insight-total" class="text-yellow fw-bold mb-0 display-6">0</h1>
                <p class="text-muted text-uppercase tracking-wide small">Ações Encontradas</p>
                
                <hr>
                
                <div id="chart-tema-container" class="mx-auto mb-4 w-75">
                    <h4 class="text-muted mb-2">Por Área Temática</h4>
                    <canvas id="chartTemas"></canvas>
                </div>
                
                <div id="chart-ods-container" class="mx-auto mb-4 w-75">
                    <h4 class="text-muted mb-2">Por ODS</h4>
                    <canvas id="chartOds"></canvas>
                </div>

                <a id="btn-detalhar-acoes" href="#" class="btn btn-brown w-100 mt-3">
                    Detalhar as ações <i class="ti ti-arrow-right ms-2"></i>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const cidadesComProjetos = {!! json_encode($cidadesAtivas) !!};
        const statsPorCidade = {!! json_encode($cidadesStats) !!};
        
        const panelFiltros = document.getElementById('panel-filtros');
        const panelInsights = document.getElementById('panel-insights');
        let chartTemaInstance = null;
        let chartOdsInstance = null;

        //essa reproducao do chart das ods é o mesmo que eu ifz na dashboard!
        const coresOficiaisOds = {
            '1': '#E5243B', '2': '#DDA63A', '3': '#4C9F38', '4': '#C5192D', '5': '#FF3A21', '6': '#26BDE2', '7': '#FCC30B',
            '8': '#A21942', '9': '#FD6925', '10': '#DD1367', '11': '#FD9D24', '12': '#BF8B2E', '13': '#3F7E44', '14': '#0A97D9',
            '15': '#56C02B', '16': '#00689D', '17': '#19486A'
        };

        const nomesOdsPT = {
            '1': 'Erradicação da Pobreza', '2': 'Fome Zero', '3': 'Saúde e Bem-Estar', '4': 'Educação de Qualidade',
            '5': 'Igualdade de Gênero', '6': 'Água Potável e Saneamento', '7': 'Energia Limpa e Acessível',
            '8': 'Trabalho Decente e Crescimento Econômico', '9': 'Indústria, Inovação e Infraestrutura',
            '10': 'Redução das Desigualdades', '11': 'Cidades e Comunidades Sustentáveis',
            '12': 'Consumo e Produção Responsáveis', '13': 'Ação Contra a Mudança Global do Clima',
            '14': 'Vida na Água', '15': 'Vida Terrestre', '16': 'Paz, Justiça e Instituições Eficazes',
            '17': 'Parcerias e Meios de Implementação'
        };

        const iconesPreCarregados = {};
        for (let i = 1; i <= 17; i++) {
            iconesPreCarregados[i] = new Image();
            iconesPreCarregados[i].src = `{{ asset('assets/img/ods') }}/${i}.png`;
        }

        const odsIconPlugin = {
            id: 'odsIcon',
            afterDatasetsDraw: function(chart) {
                const ctx = chart.ctx;
                const meta = chart.getDatasetMeta(0);
                if (!meta || !meta.data) return;

                ctx.save();
                meta.data.forEach((arc, index) => {
                    const odsNumero = chart.data.labels[index].replace('ODS ', '');
                    const angulo = (arc.startAngle + arc.endAngle) / 2;

                    const innerRadius = arc.innerRadius;
                    const outerRadius = arc.outerRadius;
                    const midRadius = innerRadius + ((outerRadius - innerRadius) / 2);

                    const inX = arc.x + Math.cos(angulo) * midRadius;
                    const inY = arc.y + Math.sin(angulo) * midRadius;

                    const icone = iconesPreCarregados[odsNumero];
                    if (icone && icone.complete && icone.naturalHeight !== 0) {
                        ctx.save();
                        ctx.filter = 'brightness(0) invert(1)'; // Força o ícone a ficar branco
                        ctx.drawImage(icone, inX - 11, inY - 11, 22, 22); // Centraliza um ícone de 22x22px
                        ctx.restore();
                    }
                });
                ctx.restore();
            }
        };

        document.querySelectorAll('.municipio').forEach(path => {
            const rawName = path.getAttribute('data-cidade');
            if (rawName) {
                path.setAttribute('data-bs-toggle', 'tooltip');
                path.setAttribute('data-bs-animation', 'false'); 
                path.setAttribute('title', rawName);
            }
            
            path.addEventListener('mouseenter', function() {
                if (this.classList.contains('cidade-ativa') && this.parentNode.lastElementChild !== this) {
                    this.parentNode.appendChild(this);
                }
            });

            path.addEventListener('mouseleave', function() {
                const tooltipInstance = bootstrap.Tooltip.getInstance(this);
                if (tooltipInstance) tooltipInstance.hide();
                document.querySelectorAll('.tooltip').forEach(t => t.remove());
            });

            path.addEventListener('click', function() {
                if (!this.classList.contains('cidade-ativa')) return;
                carregarInsights(this.getAttribute('data-cidade'));
            });
        });

        cidadesComProjetos.forEach(cidade => {
            const path = document.querySelector(`.municipio[data-cidade="${cidade}"]`);
            if (path) {
                path.classList.add('cidade-ativa');
                path.parentNode.appendChild(path);
            }
        });

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));

        const carregarInsights = (cidadeId) => {
            const dados = statsPorCidade[cidadeId];
            if (!dados) return;

            document.getElementById('insight-cidade-nome').innerText = dados.nome_limpo;
            document.getElementById('insight-total').innerText = dados.total;

            const currentParams = new URLSearchParams(window.location.search);
            
            currentParams.set('cidade', dados.nome_limpo);
            
            if (currentParams.get('situacao') === 'TODAS') {
                currentParams.delete('situacao');
            }

            document.getElementById('btn-detalhar-acoes').href = `/vitrine/catalogo?${currentParams.toString()}`;

            panelFiltros.classList.add('d-none');
            panelInsights.classList.remove('d-none');

            const temaDropdown = document.getElementById('filter-tema');
            if (temaDropdown && temaDropdown.value !== '') {
                document.getElementById('chart-tema-container').classList.add('d-none');
            } else {
                document.getElementById('chart-tema-container').classList.remove('d-none');
                desenharGraficoTema(dados.temas);
            }

            desenharGraficoOds(dados.ods);
        };

        document.getElementById('btn-voltar-filtros').addEventListener('click', () => {
            panelInsights.classList.add('d-none');
            panelFiltros.classList.remove('d-none');
        });

        const desenharGraficoTema = (dadosTemas) => {
            const ctx = document.getElementById('chartTemas');
            if (chartTemaInstance) chartTemaInstance.destroy();

            chartTemaInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(dadosTemas),
                    datasets: [{
                        data: Object.values(dadosTemas),
                        backgroundColor: ['#d946ef', '#f97316', '#84cc16', '#0891b2', '#78350f', '#0ea5e9', '#f5be56', '#334155'],
                        borderWidth: 0
                    }]
                },
                options: { cutout: '70%', plugins: { legend: { display: false } } }
            });
        };

        const desenharGraficoOds = (dadosOds) => {
            const ctx = document.getElementById('chartOds');
            if (chartOdsInstance) chartOdsInstance.destroy();

            const chaves = Object.keys(dadosOds);
            const backgroundColors = chaves.map(num => coresOficiaisOds[num] || '#532B1D');

            chartOdsInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chaves.map(num => `ODS ${num}`),
                    datasets: [{
                        data: Object.values(dadosOds),
                        backgroundColor: backgroundColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                plugins: [odsIconPlugin], 
                options: { 
                    cutout: '65%', 
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const rawNum = chaves[context[0].dataIndex];
                                    return nomesOdsPT[rawNum];
                                },
                                label: function(context) {
                                    return ` ${context.raw} ações`;
                                }
                            }
                        }
                    } 
                }
            });
        };
    });
</script>
@endsection