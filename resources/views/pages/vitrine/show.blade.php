@extends('pages.vitrine.template')

@section('styles')
<!-- garantindo a responsividade da imagem-->
<style>
    .vitrine-banner {
        max-height: 70dvh;
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
<div class="container">
    <a href="{{ url()->previous() }}" class="btn btn-yellow my-4">← Voltar Página</a>

    <div class="card">
        @php
            $temaFallback = str_replace(' ', '_', mb_strtolower($acao->area_tematica, 'UTF-8'));
            $numeroAleatorio = rand(1, 3);
            $sufixo = $numeroAleatorio === 1 ? '' : '_' . $numeroAleatorio;
            $nomeArquivoFallback = $temaFallback . $sufixo . '.png';
        @endphp

        <img src="{{ $acao->img ? asset('storage/' . $acao->img) : asset('assets/img/themes/' . $nomeArquivoFallback) }}" 
                    class="vitrine-banner card-img-top" 
                    alt="{{ $acao->alt_capa ?? 'Capa da ação ' . $acao->titulo }}">
        
        <div class="card-body px-5">
            <h1 class="mb-3">{{ $acao->titulo }}</h1>
            <div class="d-flex gap-2 mb-4">
                <span class="badge bg-blue-lt">{{ $acao->area_tematica }}</span>
                <span class="badge bg-green-lt">{{ $acao->centro_departamento_sigla }}</span>
            </div>

            <h3 class="mt-5 border-bottom border-brown">Resumo</h3>
            <div class="text-muted px-4 py-2">{!! $acao->resumo ?: 'O resumo da ação não está disponível no momento! Volte depois.' !!}</div>

            <h3 class="mt-5 border-bottom border-brown">Contato</h3>
            @php
                $membroCoordenador = $acao->equipe->first(function ($membro) {
                    $categoria = trim(strtoupper($membro->categoria_membro));
                    $validCategories = [
                        'COORDENADOR(A)', 
                    ];
                    return in_array($categoria, $validCategories);
                });

                $userCoordenador = null;
                if ($membroCoordenador) {
                    $cleanUserId = trim((string) $membroCoordenador->id_usuario);
                    $userCoordenador = \App\Models\User::where('uuid', $cleanUserId)->first();
                }
            @endphp

            @if($userCoordenador)
                <div class="bg-light px-4 py-2 rounded text-muted shadow-sm">
                    <p class="mb-1">
                        <strong>Coordenador(a):</strong> {{ $userCoordenador->name }}
                    </p>
                    <p class="mb-1">
                        <strong>Email:</strong> 
                        <a href="mailto:{{ $userCoordenador->email }}" class="text-decoration-none text-brown fw-bold">
                            {{ $userCoordenador->email }}
                        </a>
                    </p>
                    
                    @if(!empty($userCoordenador->phone))
                        <p class="mb-0">
                            <strong>Telefone:</strong> {{ $userCoordenador->phone }}
                        </p>
                    @endif
                </div>
            @else
                <div class="alert alert-warning mb-0 border-0 shadow-sm">
                    As informações de contato da coordenação não estão disponíveis no momento.
                </div>
            @endif
            
            @auth
                @if(Auth::user()->id_instituicao !== null)
                    @php
                        $cleanId = trim((string) Auth::user()->id_instituicao);
                        
                        $currentMatch = \App\Models\Match_Acao::where('id_acao', $acao->id)
                            ->where('id_instituicao', $cleanId)
                            ->first();
                    @endphp

                    <div class="mt-3 pt-1 border-top border-light mw-fit">
                    @if(!$currentMatch && $acao->situacao == 'EM EXECUÇÃO')
                        <div id="match-button-container">
                            <button type="button" class="btn btn-yellow fw-bold shadow-sm fs-3 py-3 w-auto m-0" data-bs-toggle="modal" data-bs-target="#modal-confirm-interest">
                                <i class="ti ti-heart-handshake me-2 fs-2"></i> Gostaria de uma parceria com essa ação.
                            </button>
                        </div>

                        <div class="modal fade" id="modal-confirm-interest" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fs-3 text-brown"><i class="ti ti-info-circle me-2"></i> Confirmar Interesse</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start p-4">
                                        <p class="fs-3 mb-3">Você está prestes a solicitar uma parceria com o projeto <strong>{{ $acao->titulo }}</strong>.</p>
                                        <div class="alert alert-info border-0 shadow-sm mb-0">
                                            <i class="ti ti-mail-forward me-2"></i>
                                            Ao confirmar, a coordenação da ação será notificada e receberá os dados de contato da sua instituição (Email, Telefone, Endereço e CNPJ) para dar continuidade ao processo.
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button id="btn-cancel-interest" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <form id="form-express-interest" action="{{ route('vitrine.match.interest', $acao->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button id="btn-submit-interest" type="submit" class="btn btn-yellow fw-bold shadow-sm">
                                                Sim, Confirmar interesse.
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @elseif($currentMatch && $currentMatch->mutual == false)
                            <button class="btn btn-secondary w-100 fw-bold shadow-sm fs-3 py-3 m-0 opacity-75 w-auto" disabled>
                                <i class="ti ti-clock me-2 fs-2"></i> Esperando a coordenação aprovar a parceria...
                            </button>

                        @elseif($currentMatch && $currentMatch->mutual == true)
                            <div class="btn btn-success w-100 fw-bold shadow-sm fs-4 py-3 m-0 w-auto" style="pointer-events: none;">
                                <i class="ti ti-circle-check me-2 fs-2"></i> Vínculo aceito! A coordenação da ação também tem interesse numa parceria.
                            </div>
                        @endif
                    </div>
                @endif
            @endauth

            <h3 class="mt-5 border-bottom border-brown">ODS Vinculados</h3>
            <div class="text-muted px-4 py-2">
            @if(!empty($acao->ods))
                @php
                    $odsArray = array_map('trim', explode(';', $acao->ods));
                    
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

                <div class="mx-auto" style="width:80%;">
                    <div class="row row-cols-3 row-cols-md-6 g-1 justify-content-center align-items-center p-2 shadow-sm">
                        
                        @for ($i = 1; $i <= 17; $i++)
                        @php
                            $isActive = in_array((string)$i, $odsArray);
                            $imgName = $isActive ? "{$i}.png" : "{$i}_light.png";
                            $nomeOficial = $odsNomes[$i];
                        @endphp
                        
                        <div class="col">
                            <img 
                            src="https://sig.ufca.edu.br/sigaa/img/ODS/{{ $imgName }}" 
                            class="img-fluid w-100" 
                            alt="ODS {{ $i }}: {{ $nomeOficial }}"
                            title="ODS {{ $i }}: {{ $nomeOficial }}"
                            >
                        </div>
                        @endfor
                        
                        <div class="col">
                            <img 
                            src="https://sig.ufca.edu.br/sigaa/img/ODS/ods_.png" 
                            class="img-fluid w-100" 
                            alt="Logo Geral dos Objetivos de Desenvolvimento Sustentável"
                            title="Objetivos de Desenvolvimento Sustentável"
                            >
                        </div>

                    </div>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    Nenhum ODS vinculado a esta ação no momento.
                </div>
            @endif

            </div>

            <h3 class="mt-5 border-bottom border-brown">Galeria de Imagens</h3>
            <div class="row g-3 px-4 py-2">
                @forelse($acao->galeria as $foto)
                    <div class="col-6 col-md-4 col-lg-3">
                        <img src="{{ asset('storage/' . $foto->caminho_imagem) }}" alt="{{ $foto->texto_alternativo }}" class="img-fluid rounded shadow-sm" style="aspect-ratio: 1/1; object-fit: cover;">
                    </div>
                @empty
                    <p class="text-muted p-0">Nenhuma imagem disponível na galeria.</p>
                @endforelse
            </div>

        <h3 class="mt-5 border-bottom border-brown row">Local de atuação</h3>
        <div class="px-4 py-2 col-10 col-md-6 mx-auto">
            @php
                $localidadesRaw = $acao->localidade;
                
                $cidadesAtivas = [];
                $estadosAtivos = [];

                $regioes = [
                    'Cariri' => ['Abaiara', 'Altaneira', 'Antonina do Norte', 'Araripe', 'Assaré', 'Aurora', 'Barbalha', 'Barro', 'Brejo Santo', 'Campos Sales', 'Caririaçu', 'Crato', 'Farias Brito', 'Granjeiro', 'Jardim', 'Jati', 'Juazeiro do Norte', 'Lavras da Mangabeira', 'Mauriti', 'Milagres', 'Missão Velha', 'Nova Olinda', 'Penaforte', 'Porteiras', 'Potengi', 'Salitre', 'Santana do Cariri', 'Tarrafas', 'Várzea Alegre'],
                    'Centro Sul' => ['Acopiara', 'Baixio', 'Cariús', 'Catarina', 'Cedro', 'Icó', 'Iguatu', 'Ipaumirim', 'Jucás', 'Orós', 'Quixelô', 'Saboeiro', 'Umari'],
                    'Grande Fortaleza' => ['Aquiraz', 'Cascavel', 'Caucaia', 'Chorozinho', 'Eusébio', 'Fortaleza', 'Guaiúba', 'Horizonte', 'Itaitinga', 'Maracanaú', 'Maranguape', 'Pacajus', 'Pacatuba', 'Paracuru', 'Paraipaba', 'Pindoretama', 'São Gonçalo do Amarante', 'São Luís do Curu', 'Trairi'],
                    'Litoral Leste' => ['Aracati', 'Beberibe', 'Fortim', 'Icapuí', 'Itaiçaba', 'Jaguaruana'],
                    'Litoral Norte' => ['Acaraú', 'Barroquinha', 'Bela Cruz', 'Camocim', 'Chaval', 'Cruz', 'Granja', 'Itarema', 'Jijoca de Jericoacoara', 'Marco', 'Martinópole', 'Morrinhos', 'Uruoca'],
                    'Litoral Oeste / Vale do Curu' => ['Amontada', 'Apuiarés', 'General Sampaio', 'Irauçuba', 'Itapajé', 'Itapipoca', 'Miraíma', 'Pentecoste', 'Tejuçuoca', 'Tururu', 'Umirim', 'Uruburetama'],
                    'Maciço de Baturité' => ['Acarape', 'Aracoiaba', 'Aratuba', 'Barreira', 'Baturité', 'Capistrano', 'Guaramiranga', 'Itapiúna', 'Mulungu', 'Ocara', 'Pacoti', 'Palmácia', 'Redenção'],
                    'Serra da Ibiapaba' => ['Carnaubal', 'Croatá', 'Guaraciaba do Norte', 'Ibiapina', 'Ipu', 'São Benedito', 'Tianguá', 'Ubajara', 'Viçosa do Ceará'],
                    'Sertão Central' => ['Banabuiú', 'Choró', 'Deputado Irapuan Pinheiro', 'Ibaretama', 'Ibicuitinga', 'Milhã', 'Mombaça', 'Pedra Branca', 'Piquet Carneiro', 'Quixadá', 'Quixeramobim', 'Senador Pompeu', 'Solonópole'],
                    'Sertão de Canindé' => ['Boa Viagem', 'Canindé', 'Caridade', 'Itatira', 'Madalena', 'Paramoti'],
                    'Sertão de Sobral' => ['Alcântaras', 'Cariré', 'Coreaú', 'Forquilha', 'Frecheirinha', 'Graça', 'Groaíras', 'Massapê', 'Meruoca', 'Moraújo', 'Mucambo', 'Pacujá', 'Pires Ferreira', 'Reriutaba', 'Santana do Acaraú', 'Senador Sá', 'Sobral', 'Varjota'],
                    'Sertão dos Crateús' => ['Ararendá', 'Catunda', 'Crateús', 'Hidrolândia', 'Independência', 'Ipaporanga', 'Ipueiras', 'Monsenhor Tabosa', 'Nova Russas', 'Novo Oriente', 'Poranga', 'Santa Quitéria', 'Tamboril'],
                    'Sertão dos Inhamuns' => ['Aiuaba', 'Arneiroz', 'Parambu', 'Quiterianópolis', 'Tauá'],
                    'Vale do Jaguaribe' => ['Alto Santo', 'Ererê', 'Iracema', 'Jaguaretama', 'Jaguaribara', 'Jaguaribe', 'Limoeiro do Norte', 'Morada Nova', 'Palhano', 'Pereiro', 'Potiretama', 'Quixeré', 'Russas', 'São João do Jaguaribe', 'Tabuleiro do Norte']
                ];

                foreach ($localidadesRaw as $loc) {
                    $cidadeNome = trim((string) $loc->cidade);
                    $estado = trim((string) $loc->estado);
                    
                    if (empty($cidadeNome) || empty($estado)) continue;

                    if ($estado === 'Ceará') $estado = 'CE';
                    else if ($estado === 'Pernambuco') $estado = 'PE';

                    $cidadesAtivas[] = [
                        'cidade' => $cidadeNome,
                        'estado' => $estado
                    ];

                    if (!isset($estadosAtivos[$estado])) {
                        $estadosAtivos[$estado] = [];
                    }

                    if ($estado === 'CE') {
                        foreach ($regioes as $regiaoNome => $cidades) {
                            if (in_array($cidadeNome, $cidades)) {
                                if (!in_array($regiaoNome, $estadosAtivos['CE'])) {
                                    $estadosAtivos['CE'][] = $regiaoNome;
                                }
                                break;
                            }
                        }
                    }
                }

                $componentesAtivos = [];
                $titulosParaExibir = [];

                foreach ($estadosAtivos as $uf => $regioesDoEstado) {
                    if ($uf === 'CE') {
                        if (count($regioesDoEstado) === 1) {
                            $regiao = $regioesDoEstado[0];
                            $titulosParaExibir[] = $regiao;
                            $comp = str_replace([' ', '/'], ['-', ''], strtolower(preg_replace('/[áàãâä]/ui', 'a', preg_replace('/[éèêë]/ui', 'e', preg_replace('/[íìîï]/ui', 'i', preg_replace('/[óòõôö]/ui', 'o', preg_replace('/[úùûü]/ui', 'u', preg_replace('/[ç]/ui', 'c', $regiao))))))));
                            $componentesAtivos[] = preg_replace('/-+/', '-', $comp);
                        } else if (count($regioesDoEstado) > 1) {
                            $titulosParaExibir[] = 'Estado do Ceará (Múltiplas Regiões)';
                            $componentesAtivos[] = 'ceara';
                        }
                    } else if ($uf === 'PE') {
                        $titulosParaExibir[] = 'Pernambuco';
                        $componentesAtivos[] = 'pernambuco';
                    }
                }
            @endphp

            <div class="position-relative w-100 w-lg-75 mx-auto rounded p-3 shadow-sm border" style="background-color: #F7F5F0;">
                
                @if(empty($componentesAtivos))
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center bg-white bg-opacity-75 z-1" style="backdrop-filter: blur(4px);">
                        <span class="badge bg-secondary fs-4 shadow-sm text-uppercase mb-2">Sem dados de mapa no momento</span>
                    </div>
                    <div style="opacity: 0.4;"><x-mapas.mapa-cariri /></div>
                    
                @else
                    <div id="dynamic-map-container" style="min-height: 250px;">
                        <h4 class="text-center text-muted mb-3 fw-bold text-uppercase">{{ implode(' - ', $titulosParaExibir) }}</h4>
                        
                        <div class="row row-cols-1 row-cols-md-{{ count($componentesAtivos) > 1 ? '2' : '1' }} g-2 align-items-center justify-content-center">
                            @foreach($componentesAtivos as $componente)
                                <div class="col">
                                    @if($componente == 'ceara') <x-mapas.mapa-ceara />
                                    @elseif($componente == 'cariri') <x-mapas.mapa-cariri />
                                    @elseif($componente == 'centro-sul') <x-mapas.mapa-centro-sul />
                                    @elseif($componente == 'grande-fortaleza') <x-mapas.mapa-grande-fortaleza />
                                    @elseif($componente == 'litoral-leste') <x-mapas.mapa-litoral-leste />
                                    @elseif($componente == 'litoral-norte') <x-mapas.mapa-litoral-norte />
                                    @elseif($componente == 'litoral-oeste-vale-do-curu') <x-mapas.mapa-litoral-oeste-vale-do-curu />
                                    @elseif($componente == 'macico-de-baturite') <x-mapas.mapa-macico-de-baturite />
                                    @elseif($componente == 'serra-da-ibiapaba') <x-mapas.mapa-serra-da-ibiapaba />
                                    @elseif($componente == 'sertao-central') <x-mapas.mapa-sertao-central />
                                    @elseif($componente == 'sertao-de-caninde') <x-mapas.mapa-sertao-de-caninde />
                                    @elseif($componente == 'sertao-de-sobral') <x-mapas.mapa-sertao-de-sobral />
                                    @elseif($componente == 'sertao-dos-crateus') <x-mapas.mapa-sertao-dos-crateus />
                                    @elseif($componente == 'sertao-dos-inhamuns') <x-mapas.mapa-sertao-dos-inhamuns />
                                    @elseif($componente == 'vale-do-jaguaribe') <x-mapas.mapa-vale-do-jaguaribe />
                                    @elseif($componente == 'pernambuco') <x-mapas.mapa-pernambuco />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const cidadesAtivas = @json($cidadesAtivas);
        
        if (cidadesAtivas && cidadesAtivas.length > 0) {
            
            document.querySelectorAll('.municipio').forEach(cidade => {
                cidade.style.fill = '#ffffff';
                cidade.style.stroke = '#cccccc'; 
                cidade.style.transition = 'none';
            });
            
            cidadesAtivas.forEach(local => {
                const query = `.municipio[data-cidade="${local.cidade}" i][data-estado="${local.estado}" i]`;
                const paths = document.querySelectorAll(query);
                
                paths.forEach(targetCity => {
                    targetCity.style.fill = 'var(--tblr-brown)'; 
                    targetCity.style.stroke = 'var(--tblr-yellow)'; 
                    targetCity.style.strokeWidth = '1.5px';
                    
                    targetCity.setAttribute('data-bs-toggle', 'tooltip');
                    targetCity.setAttribute('data-bs-placement', 'top');
                    targetCity.setAttribute('title', `${local.cidade} (${local.estado})`);
                    
                    targetCity.parentNode.appendChild(targetCity);
                    
                    new bootstrap.Tooltip(targetCity);
                });
            });
        }

        const form = document.getElementById('form-express-interest');
        const submitBtn = document.getElementById('btn-submit-interest');
        const cancelBtn = document.getElementById('btn-cancel-interest');
        const container = document.getElementById('match-button-container');
    
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processando...';
                submitBtn.disabled = true;
                cancelBtn.disabled = true;
    
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modalEl = document.getElementById('modal-confirm-interest');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        modalInstance.hide();
    
                        if (container) {
                            container.innerHTML = `
                                <div class="alert alert-success shadow-sm d-inline-flex align-items-center mb-0 fs-3 py-3">
                                    <i class="ti ti-check fs-2 me-2"></i> 
                                    <strong>Interesse registrado!</strong>&nbsp;A coordenação foi notificada.
                                </div>
                            `;
                        }
                    } else {
                        alert(data.message);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocorreu um erro ao processar a solicitação.');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }
    });

</script>
@endsection