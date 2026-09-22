@extends('pages.vitrine.template')

@section('styles')
<!-- garantindo a responsividade da imagem-->
<style>
    .vitrine-banner {
        max-height: 80dvh;
        width: 100%;
        object-fit: cover;
        aspect-ratio: 16 / 9;
    }
    @media (max-width: 768px) {
        .vitrine-banner {
            aspect-ratio: 1 / 1;
        }
    }
</style>
@endsection

@section('content')

<div id="mainVitrineCarousel" class="carousel slide mt-4" data-bs-ride="carousel">
    
    <div class="carousel-inner rounded-3 shadow-sm">
        @forelse($mainCarousel as $index => $acao)
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
            
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <a href="{{ route('vitrine.show', $acao->id) }}" class="d-block text-decoration-none">
                    <img src="{{ $acao->img ? asset('storage/' . $acao->img) : asset('assets/img/themes/' . $nomeArquivoFallback) }}" class="vitrine-banner" alt="{{ $acao->alt_capa ?? 'Capa da ação ' . $acao->titulo }}">
                    
                    <div class="carousel-caption pb-4 d-flex justify-content-center">
                        
                        <div class="rounded px-4 py-3 d-flex flex-column align-items-center gap-2" 
                            style="background-color: {{ $corTema }}50; width: fit-content; max-width: 100%;">
                            
                            <h3 class="text-white title-clamp m-0 lh-base text-center" title="{{ $acao->titulo }}" style="font-size: clamp(1.5rem, 4vw, 2rem);">
                                {{ $acao->titulo }}
                            </h3>
                            
                            <span class="badge" style="background-color: #ffffff80; color: {{ $corTema }}; border: none;">
                                {{ mb_strtoupper($acao->area_tematica, 'UTF-8') }}
                            </span>
                            
                        </div>
                        
                    </div>
                </a>
            </div>
        @empty
            <div class="carousel-item active">
                <div class="d-flex align-items-center justify-content-center bg-light" style="height: 400px;">
                    <p class="text-muted">Nenhuma ação em destaque.</p>
                </div>
            </div>
        @endforelse
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#mainVitrineCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainVitrineCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>

@foreach($thematicAreas as $area => $acoes)
    <div class="mt-5 position-relative">
        <h4 class="mb-3 border-bottom border-brown pb-2">{{ $area }}</h4>

        <button class="btn btn-icon btn-light position-absolute start-0 translate-middle-y rounded-circle shadow d-none d-md-flex" 
                style="top: 55%; z-index: 10; margin-left: -15px;"
                onclick="document.getElementById('carousel-{{ $loop->index }}').scrollBy({ left: -500, behavior: 'smooth' })">
            <i class="ti ti-chevron-left fs-2"></i>
        </button>

        <div id="carousel-{{ $loop->index }}" class="d-flex overflow-auto gap-3 pb-3 hide-scrollbar" style="scroll-snap-type: x mandatory;">
            @foreach($acoes as $acao)
                @php
                    $temaFallback = str_replace(' ', '_', mb_strtolower($acao->area_tematica, 'UTF-8'));
                    $numeroAleatorio = rand(1, 3);
                    $sufixo = $numeroAleatorio === 1 ? '' : '_' . $numeroAleatorio;
                    $nomeArquivoFallback = $temaFallback . $sufixo . '.png';
                @endphp
                <div class="card card-sm flex-shrink-0 shadow-sm" style="width: 280px; scroll-snap-align: start;">
                    <a href="{{ route('vitrine.show', $acao->id) }}" class="text-decoration-none text-reset">
                        <img src="{{ $acao->img ? asset('storage/' . $acao->img) : asset('assets/img/themes/' . $nomeArquivoFallback) }}" 
                        class="vitrine-banner" 
                        alt="{{ $acao->alt_capa ?? 'Capa da ação ' . $acao->titulo }}">
                        <div class="card-body">
                            <h5 class="card-title title-clamp fs-4 m-0" title="{{ $acao->titulo }}">{{ $acao->titulo }}</h5>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <button class="btn btn-icon btn-light position-absolute end-0 translate-middle-y rounded-circle shadow d-none d-md-flex" 
                style="top: 55%; z-index: 10; margin-right: -15px;"
                onclick="document.getElementById('carousel-{{ $loop->index }}').scrollBy({ left: 500, behavior: 'smooth' })">
            <i class="ti ti-chevron-right fs-2"></i>
        </button>
    </div>
@endforeach
@endsection
@section('scripts')
@endsection