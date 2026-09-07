@extends('pages.vitrine.template')

@section('styles')
<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: all .2s ease-in-out;
    }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row mb-4 border-bottom border-brown pb-3">
        <div class="col">
            <h1 class="text-brown m-0">Catálogo das Ações de Extensão</h1>
            <p class="text-muted m-0 mt-1">Explore e filtre todas as ações de extensão cadastradas.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-3">
            <div class="card shadow-sm sticky-top" style="top: 2rem;">
                <div class="card-header bg-light">
                    <h3 class="card-title text-brown m-0">Filtros</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('vitrine.catalogo') }}" method="GET">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Área Temática</label>
                            <select name="area_tematica" class="form-select">
                                <option value="">Todas as áreas</option>
                                <option value="Comunicação" {{ request('area_tematica') == 'Comunicação' ? 'selected' : '' }}>Comunicação</option>
                                <option value="Cultura" {{ request('area_tematica') == 'Cultura' ? 'selected' : '' }}>Cultura</option>
                                <option value="Direitos Humanos e Justiça" {{ request('area_tematica') == 'Direitos Humanos e Justiça' ? 'selected' : '' }}>Direitos Humanos e Justiça</option>
                                <option value="Educação" {{ request('area_tematica') == 'Educação' ? 'selected' : '' }}>Educação</option>
                                <option value="Meio Ambiente" {{ request('area_tematica') == 'Meio Ambiente' ? 'selected' : '' }}>Meio Ambiente</option>
                                <option value="Saúde" {{ request('area_tematica') == 'Saúde' ? 'selected' : '' }}>Saúde</option>
                                <option value="Tecnologia e Produção" {{ request('area_tematica') == 'Tecnologia e Produção' ? 'selected' : '' }}>Tecnologia e Produção</option>
                                <option value="Trabalho" {{ request('area_tematica') == 'Trabalho' ? 'selected' : '' }}>Trabalho</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Tipo de Ação</label>
                            <select name="tipo_acao" class="form-select">
                                <option value="">Todos os tipos</option>
                                <option value="PROGRAMA" {{ request('tipo_acao') == 'PROGRAMA' ? 'selected' : '' }}>Programa</option>
                                <option value="PROJETO" {{ request('tipo_acao') == 'PROJETO' ? 'selected' : '' }}>Projeto</option>
                                <option value="CURSO" {{ request('tipo_acao') == 'CURSO' ? 'selected' : '' }}>Curso</option>
                                <option value="EVENTO" {{ request('tipo_acao') == 'EVENTO' ? 'selected' : '' }}>Evento</option>
                                <option value="PRESTAÇÃO DE SERVIÇO" {{ request('tipo_acao') == 'PRESTAÇÃO DE SERVIÇO' ? 'selected' : '' }}>Prestação de Serviço</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Situação</label>
                            <select name="situacao" class="form-select">
                                <option value="">Todas as situações</option>
                                <option value="EM EXECUÇÃO" {{ request('situacao') == 'EM EXECUÇÃO' ? 'selected' : '' }}>Em Execução</option>
                                <option value="CONCLUÍDA" {{ request('situacao') == 'CONCLUÍDA' ? 'selected' : '' }}>Concluída</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">ODS</label>
                                <select name="ods" class="form-select">
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
                        </div>

                        <div class="d-flex flex-column gap-2 mt-4">
                            <button type="submit" class="btn btn-brown w-100">
                                <i class="ti ti-filter me-2"></i> Aplicar Filtros
                            </button>
                            <a href="{{ route('vitrine.catalogo') }}" class="btn btn-outline-warning w-100 hover">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-9">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                @forelse($acoes as $acao)
                    @php
                        $temaFallback = str_replace(' ', '_', mb_strtolower($acao->area_tematica, 'UTF-8'));
                        $numeroAleatorio = rand(1, 3);
                        $sufixo = $numeroAleatorio === 1 ? '' : '_' . $numeroAleatorio;
                        $nomeArquivoFallback = $temaFallback . $sufixo . '.png';
                    @endphp
                    
                    <div class="col">
                        <div class="card card-sm shadow-sm h-100 hover-shadow border-0">
                            <a href="{{ route('vitrine.show', $acao->id) }}" class="text-decoration-none text-reset d-flex flex-column h-100">
                                
                                <img src="{{ $acao->img ? asset('storage/' . $acao->img) : asset('assets/img/themes/' . $nomeArquivoFallback) }}" 
                                     class="card-img-top w-100" 
                                     style="height: 180px; object-fit: cover;"
                                     alt="{{ $acao->alt_capa ?? 'Capa da ação ' . $acao->titulo }}"
                                     title="{{ $acao->titulo }}">
                                     
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title title-clamp fs-4 mb-2" title="{{ $acao->titulo }}">{{ $acao->titulo }}</h5>
                                    
                                    <div class="mt-auto pt-3 d-flex gap-2 flex-wrap">
                                    @php
                                        $temaString = mb_strtolower($acao->area_tematica, 'UTF-8');
                                        $temaFormatado = str_replace(' ', '_', $temaString);
                                        $numeroAleatorio = rand(1, 3);
                                        $sufixo = $numeroAleatorio === 1 ? '' : '_' . $numeroAleatorio;
                                        $nomeArquivoFallback = $temaFormatado . $sufixo . '.png';

                                        $mapaCores = [
                                            'saúde' => '#d946ef',
                                            'educação' => '#f97316',
                                            'meio ambiente' => '#84cc16',
                                            'tecnologia e produção' => '#0891b2',
                                            'trabalho' => '#78350f',
                                            'comunicação' => '#0ea5e9',
                                            'cultura' => '#f5be56',
                                            'direitos humanos e justiça' => '#334155'
                                        ];
                                        
                                        $corTema = $mapaCores[$temaString] ?? '#334155';
                                    @endphp

                                        <span class="badge border-0" style=" background: {{$corTema}}">{{ $acao->area_tematica }}</span>
                                        <span class="badge {{ $acao->situacao === 'CONCLUÍDA' ? 'bg-green-lt' : 'bg-blue-lt' }} border-0">
                                            {{ $acao->situacao }}
                                        </span>
                                    </div>
                                </div>
                                
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 w-100">
                        <div class="alert alert-info border-0 shadow-sm">
                            Nenhuma ação encontrada com os filtros selecionados. Tente limpar a busca.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $acoes->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection