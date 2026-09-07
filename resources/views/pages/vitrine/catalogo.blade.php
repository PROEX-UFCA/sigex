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
            <h1 class="text-brown m-0">Catálogo de Projetos</h1>
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
                    <p class="text-muted small">Os dropdowns de filtro serão construídos aqui.</p>
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
                                        <span class="badge bg-yellow-lt border-0">{{ $acao->area_tematica }}</span>
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