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
        fill: var(--tblr-yellow, #F5BE56) !important; 
        stroke: var(--tblr-brown, #532B1D) !important;
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
    .minimap-wrapper {
        width: 100%;
        min-width: 350px;
        transition: transform 0.2s ease;
    }
    .minimap-wrapper:hover {
        transform: scale(1.02);
    }
    
    }
    .macro-regiao {
        cursor: pointer;
        transition: fill 0.2s ease;
    }
    .macro-regiao:hover {
        filter: brightness(0.8);
    }
    .macro-regiao.has-projects {
        fill: var(--tblr-yellow, #F5BE56) !important;
        
        stroke: var(--tblr-brown, #532B1D) !important;
        stroke-width: 3px !important;
        stroke-linejoin: round;
        
        animation: pulse-yellow 2s infinite;
    }

    @keyframes pulse-yellow {
        0% { filter: drop-shadow(0 0 0 rgba(245, 190, 86, 0.7)); }
        70% { filter: drop-shadow(0 0 8px rgba(245, 190, 86, 0)); }
        100% { filter: drop-shadow(0 0 0 rgba(245, 190, 86, 0)); }
    }
</style>
@endsection

@section('content')
<div class="row g-4 mt-2">
    <div class="col-12 col-lg-9">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 class="card-title text-brown m-0 fw-bold">
                    Você está visualizando a região: <span id="current-region-name" class="text-yellow">Cariri</span>
                </h3>
                <button id="btn-reset-map" class="btn btn-sm btn-outline-brown d-none bg-white shadow-sm">
                    <i class="ti ti-arrow-left me-1"></i> Voltar para Cariri
                </button>
            </div>
            
            <div class="card-body p-4">
                <div class="row align-items-center h-100">
                    
                    <div class="col-12 col-md-8 col-lg-8">
                        <div class="map-container w-100 mx-auto" style="min-height: 350px;">
                            <div id="view-cariri" class="region-view">
                                <x-mapas.mapa-cariri />
                            </div>
                            <div id="view-centro-sul" class="region-view d-none">
                                <x-mapas.mapa-centro-sul />
                            </div>
                            <div id="view-sertao-dos-inhamuns" class="region-view d-none">
                                <x-mapas.mapa-sertao-dos-inhamuns />
                            </div>
                            <div id="view-sertao-central" class="region-view d-none">
                                <x-mapas.mapa-sertao-central />
                            </div>
                            <div id="view-vale-do-jaguaribe" class="region-view d-none">
                                <x-mapas.mapa-vale-do-jaguaribe />
                            </div>
                            <div id="view-sertao-dos-crateus" class="region-view d-none">
                                <x-mapas.mapa-sertao-dos-crateus />
                            </div>
                            <div id="view-sertao-de-sobral" class="region-view d-none">
                                <x-mapas.mapa-sertao-de-sobral />
                            </div>
                            <div id="view-litoral-norte" class="region-view d-none">
                                <x-mapas.mapa-litoral-norte />
                            </div>
                            <div id="view-serra-da-ibiapaba" class="region-view d-none">
                                <x-mapas.mapa-serra-da-ibiapaba />
                            </div>
                            <div id="view-macico-de-baturite" class="region-view d-none">
                                <x-mapas.mapa-macico-de-baturite />
                            </div>
                            <div id="view-litoral-leste" class="region-view d-none">
                                <x-mapas.mapa-litoral-leste />
                            </div>
                            <div id="view-litoral-oeste-vale-do-curu" class="region-view d-none">
                                <x-mapas.mapa-litoral-oeste-vale-do-curu />
                            </div>
                            <div id="view-grande-fortaleza" class="region-view d-none">
                                <x-mapas.mapa-grande-fortaleza />
                            </div>
                            <div id="view-sertao-de-caninde" class="region-view d-none">
                                <x-mapas.mapa-sertao-de-caninde />
                            </div>
                            <div id="view-pernambuco" class="region-view d-none">
                                <x-mapas.mapa-pernambuco />
                            </div>
                            <div id="view-paraiba" class="region-view d-none">
                                <x-mapas.mapa-paraiba />
                            </div>
                            <div id="view-rio-grande-do-norte" class="region-view d-none">
                                <x-mapas.mapa-rio-grande-do-norte />
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4 col-lg-4 border-start">
                        <h4 class="text-center text-muted mb-3 small fw-bold text-uppercase">Navegação</h4>
                        <div class="minimap-wrapper mx-auto">
                            <x-mapas.mapa-minimapa />
                        </div>
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

                    <div class="d-flex flex-column gap-2"
                // Pass both the formatted ID and the raw name to the switcher>
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
    const normalizeToId = (str) => {
        if (!str) return '';
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    };

    document.addEventListener("DOMContentLoaded", () => {
        const cidadesComProjetos = {!! json_encode($cidadesAtivas) !!};
        const statsPorCidade = {!! json_encode($cidadesStats) !!};
        
        const btnReset = document.getElementById('btn-reset-map');
        const regionTitle = document.getElementById('current-region-name');
        const regionViews = document.querySelectorAll('.region-view');

        const switchRegionView = (viewFormat, rawName) => {
            const targetView = document.getElementById(`view-${viewFormat}`);
            regionViews.forEach(view => view.classList.add('d-none'));
            
            if (targetView) {
                targetView.classList.remove('d-none');
                regionTitle.innerText = rawName;
                
                if (viewFormat !== 'cariri') {
                    btnReset.classList.remove('d-none');
                } else {
                    btnReset.classList.add('d-none');
                }
            } else {
                document.getElementById('view-cariri').classList.remove('d-none');
                btnReset.classList.add('d-none');
                regionTitle.innerText = 'Cariri';
                console.warn(`O mapa detalhado para view-${viewFormat} ainda não foi incluído no HTML.`);
            }
        };

        btnReset.addEventListener('click', () => switchRegionView('cariri', 'Cariri'));

        document.querySelectorAll('.minimap-wrapper .macro-regiao').forEach(chunk => {
            const regiaoNome = chunk.getAttribute('data-regiao');
            
            let isActive = false;
            Object.values(statsPorCidade).forEach(dados => {
                if (dados.macroregiao === regiaoNome) isActive = true;
            });

            chunk.setAttribute('data-bs-toggle', 'tooltip');

            if (isActive) {
                chunk.classList.add('has-projects');
                chunk.setAttribute('title', `Ver ações em: ${regiaoNome}`);
                
                chunk.parentNode.appendChild(chunk);
            } else {
                chunk.setAttribute('title', regiaoNome);
            }

            chunk.addEventListener('click', function() {
                const normalizedId = normalizeToId(regiaoNome);
                switchRegionView(normalizedId, regiaoNome);
                
                const tooltipInstance = bootstrap.Tooltip.getInstance(this);
                if (tooltipInstance) tooltipInstance.hide();
            });
        });

        console.log("Cidades Ativas Data:", cidadesComProjetos);

        cidadesComProjetos.forEach(local => {
            let nomeCidade = '';
            let nomeEstado = '';

            if (local.cidade && local.estado) {
                nomeCidade = local.cidade;
                nomeEstado = local.estado;
            } else if (Array.isArray(local) && local.length >= 2) {
                nomeCidade = local[0];
                nomeEstado = local[1];
            } else {
                return; 
            }

            const query = `.municipio[data-cidade="${nomeCidade}" i][data-estado="${nomeEstado}" i]`;
            const paths = document.querySelectorAll(query);
            
            if (paths.length > 0) {
                paths.forEach(path => {
                    path.classList.add('cidade-ativa');
                    
                    path.parentNode.appendChild(path);
                });
            } else {
                console.warn(`Cidade não encontrada no SVG: ${nomeCidade} (${nomeEstado})`);
            }
        });

        const bindMunicipalityEvents = () => {
             document.querySelectorAll('.municipio').forEach(path => {
                const rawName = path.getAttribute('data-cidade');
                const rawEstado = path.getAttribute('data-estado'); 
                
                if (!rawName) return; 
                
                const tooltipText = rawEstado ? `${rawName} (${rawEstado})` : rawName;
                
                path.setAttribute('data-bs-toggle', 'tooltip');
                path.setAttribute('title', tooltipText); 
                
                path.addEventListener('mouseenter', function() {
                    if (this.classList.contains('cidade-ativa') && this.parentNode.lastElementChild !== this) {
                        this.parentNode.appendChild(this);
                    }
                });

                path.addEventListener('click', function() {
                    if (!this.classList.contains('cidade-ativa')) return;
                    
                    document.querySelectorAll('.tooltip').forEach(t => t.remove());
                    
                    const uniqueKey = this.getAttribute('data-cidade') + '-' + this.getAttribute('data-estado');
                    carregarInsights(uniqueKey);
                });
            });
        };
        
        bindMunicipalityEvents();

        new bootstrap.Tooltip(document.body, {
            selector: '[data-bs-toggle="tooltip"]',
            container: 'body',
            trigger: 'hover'
        });

        document.body.addEventListener('mouseleave', (event) => {
            if (event.target && typeof event.target.matches === 'function' && event.target.matches('[data-bs-toggle="tooltip"]')) {
                document.querySelectorAll('.tooltip').forEach(t => t.remove());
            }
        }, true);

        const panelFiltros = document.getElementById('panel-filtros');
        const panelInsights = document.getElementById('panel-insights');
        let chartTemaInstance = null;
        let chartOdsInstance = null;

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
                        ctx.filter = 'brightness(0) invert(1)';
                        ctx.drawImage(icone, inX - 11, inY - 11, 22, 22);
                        ctx.restore();
                    }
                });
                ctx.restore();
            }
        };

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