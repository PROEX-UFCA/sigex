@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-header">
    <div>
        <h2 class="mb-1">Sincronização de dados</h2>
        <p class="text-muted mb-0 mt-0">
            Página destiada para sincronizar os dados locais (ações, membros e localizações) com os dados do sigaa.
        </p>
    </div>
</div>
<div class="page-body row">
    <div class="col-12 col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="alert"><strong>Área temporária!</strong> Indicada para o desenvolvimento local.</div>
                <small class="text-muted fw-bold text-uppercase d-block mb-2">Área das ações</small>
        
                @can('adicionar_ação')
                <a href="{{route('actions.create')}}" class="btn btn-primary w-100 mb-2 btn-sm rounded-2">Inserir ação</a>
                @endcan
        
                @can('importar_ações')
                <bottom class="btn btn-outline-dark w-100 mb-2 btn-sm rounded-2" data-bs-toggle="modal"
                    data-bs-target="#importar" aria-expanded="false" aria-controls="modalExample">Importar ações</bottom>
                <x-modal.modal id="importar" class="modal-center" title="Importar dados"
                    route="{{route('actions.previewImport')}}" textBtnClose="Cancelar" textBtnSave="Importar"
                    classBtnSave="btn-primary">
                    <x-slot:content>
                        <div class="card-body mb-3">
                            <h3>Atenção para a Importação de Dados</h3>
                            <p>
                                Para garantir que a importação ocorra sem erros, seu arquivo <strong>.csv</strong> precisa
                                conter as
                                seguintes <strong>20 colunas</strong>, exatamente nesta ordem e com esta nomenclatura no
                                cabeçalho:
                            </p>
        
                            <div class="mb-3">
                                <span class="badge badge-dark mb-1">ID_PROJETO</span>
                                <span class="badge badge-dark mb-1">ANO</span>
                                <span class="badge badge-dark mb-1">TITULO</span>
                                <span class="badge badge-dark mb-1">EDITAL</span>
                                <span class="badge badge-dark mb-1">BOLSAS_SOLICITADAS</span>
                                <span class="badge badge-dark mb-1">BOLSAS_CONCEDIDAS</span>
                                <span class="badge badge-dark mb-1">FINANCIAMENTO_INTERNO</span>
                                <span class="badge badge-dark mb-1">FINANCIAMENTO_EXTERNO</span>
                                <span class="badge badge-dark mb-1">SITUACAO</span>
                                <span class="badge badge-dark mb-1">DATA_CADASTRO</span>
                                <span class="badge badge-dark mb-1">DATA_INICIO</span>
                                <span class="badge badge-dark mb-1">DATA_FIM</span>
                                <span class="badge badge-dark mb-1">DATA_ATUALIZACAO</span>
                                <span class="badge badge-dark mb-1">SIGLA</span>
                                <span class="badge badge-dark mb-1">TIPO_ACAO</span>
                                <span class="badge badge-dark mb-1">AREA_TEMATICA</span>
                                <span class="badge badge-dark mb-1">RESUMO</span>
                                <span class="badge badge-dark mb-1">PALAVRAS_CHAVE</span>
                                <span class="badge badge-dark mb-1">ODS</span>
                                <span class="badge badge-dark mb-1">CONTEXTO</span>
                            </div>
        
                            <p><strong class="text-danger">Importante:</strong> O cabeçalho (primeira linha) do arquivo deve ter
                                <strong>exatamente</strong> os nomes acima.
                            </p>
                        </div>
                        <div id="drop-area"
                            class="rounded-4 d-flex flex-column justify-content-center align-items-center bg-light p-4 text-center"
                            style="height: 150px; cursor: pointer; border: dashed 2px gray">
                            <p class="text-muted mb-2">Arraste o .csv aqui ou clique para selecionar</p>
                            <p class="text-red mb-2">Máximo 10MB</p>
                            <input type="file" name="csv" id="csv" accept=".csv" required hidden>
                            <div id="file-info" class="text-muted small mt-2"></div>
                        </div>
                    </x-slot:content>
                </x-modal.modal>
                @endcan
        
                @can('importar_membros')
                <bottom class="btn btn-outline-dark w-100 btn-sm rounded-2" data-bs-toggle="modal"
                    data-bs-target="#importar-membros" aria-expanded="false" aria-controls="modalExample">Importar membros
                </bottom>
                <x-modal.modal id="importar-membros" class="modal-center" title="Importar dados"
                    route="{{route('membros.previewImport')}}" textBtnClose="Cancelar" textBtnSave="Importar"
                    classBtnSave="btn-primary">
                    <x-slot:content>
                        <div class="card-body mb-3">
                            <h3>Atenção para a Importação de Dados</h3>
                            <p>
                                Para garantir que a importação ocorra sem erros, seu arquivo <strong>.csv</strong> precisa
                                conter as
                                seguintes <strong>10 colunas</strong>, exatamente nesta ordem e com esta nomenclatura no
                                cabeçalho:
                            </p>
        
                            <div class="mb-3">
                                <span class="badge badge-dark mb-1">ID_PROJETO</span>
                                <span class="badge badge-dark mb-1">ID_PESSOA</span>
                                <span class="badge badge-dark mb-1">NOME</span>
                                <span class="badge badge-dark mb-1">SEXO</span>
                                <span class="badge badge-dark mb-1">E-MAIL</span>
                                <span class="badge badge-dark mb-1">DATA_INICIO</span>
                                <span class="badge badge-dark mb-1">DATA_FIM</span>
                                <span class="badge badge-dark mb-1">STATUS</span>
                                <span class="badge badge-dark mb-1">TIPO_MEMBRO</span>
                                <span class="badge badge-dark mb-1">CATEGORIA_MEMBRO</span>
                            </div>
        
                            <p><strong class="text-danger">Importante:</strong> O cabeçalho (primeira linha) do arquivo deve ter
                                <strong>exatamente</strong> os nomes acima.
                            </p>
                        </div>
                        <div id="drop-area-2"
                            class="rounded-4 d-flex flex-column justify-content-center align-items-center bg-light p-4 text-center"
                            style="height: 150px; cursor: pointer; border: dashed 2px gray">
                            <p class="text-muted mb-2">Arraste o .csv aqui ou clique para selecionar</p>
                            <p class="text-red mb-2">Máximo 10MB</p>
                            <input type="file" name="csv" id="csv-2" accept=".csv" required hidden>
                            <div id="file-info-2" class="text-muted small mt-2"></div>
                        </div>
                    </x-slot:content>
                </x-modal.modal>
                @endcan
            </div>
        </div>
    </div>
    <div class="col-12 col-md-9">
        <div class="mb-3 card">
            <div class="card-body d-flex justify-content-between align-items-center p-3">
                <div class="alert p-2 my-auto">
                    Para sincronizar, certifique-se de que está conectado a rede local da UFCA.
                </div>
                <form method="POST" action="{{ route('dados.sincronizar') }}" class="p-0">
                    @csrf
                    <button type="submit" class="btn btn-green" id="btnSincronizar">
                        <i class="icon ti ti-refresh"></i>
                        Sincronizar
                    </button>
                </form>
            </div>
        </div>
        <div>
            <div id="listaSincronizacoes" class="row g-3" style="position: relative; min-height: 200px;">
                @forelse($sincronizacoes as $s)
                {{-- <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <strong>
                                    Sincronização #{{ $s->id }}
                                </strong>

                                <span class="badge
                    @if($s->status === 'concluida') bg-success
                    @elseif($s->status === 'falhou') bg-danger
                    @elseif($s->status === 'executando') bg-primary
                    @else bg-secondary
                    @endif">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </div>

                            <p class="mb-2">
                                <strong>Etapa:</strong>
                                {{ $s->etapa_atual }}
                            </p>

                            <p class="small text-muted mb-2">
                                Início:
                                {{ date('d/m/Y H:i:s', strtotime($s->iniciado_em))
                                ?? 'Aguardando' }}
                            </p>

                            <p class="small text-muted mb-3">
                                Término:
                                {{ date('d/m/Y H:i:s', strtotime($s->finalizado_em))
                                ?? 'Em andamento' }}
                            </p>

                            @if($s->resultados)
                            <hr class="my-2">
                            <strong>Registros processados</strong>

                            <ul class="small mt-2 mb-0">
                                @foreach($s->resultados as $etapa => $total)
                                <li>
                                    {{ ucfirst(str_replace('_', ' ', $etapa)) }}:
                                    {{ $total }}
                                </li>
                                @endforeach
                            </ul>
                            @endif

                            @if($s->erro)
                            <div class="alert alert-danger mt-3 mb-0 small">
                                {{ $s->erro }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div> --}}
                @empty
                <div class="col-12">
                    <div class="alert alert-light border">
                        Nenhuma sincronização realizada ainda.
                    </div>
                </div>
                @endforelse
            </div>
            <div class="d-flex justify-content-center mt-5">
                {{ $sincronizacoes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const lista = document.getElementById('listaSincronizacoes');
        const botao = document.getElementById('btnSincronizar');
        const urlStatus = @json(route('dados.status'));
        var paginaAtual = 1;  
        
        async function atualizarStatus() {
            try {
                const urlStatusBase = @json(route('dados.status'));
        
                const urlComPagina = `${urlStatusBase}?page=${paginaAtual}`;
                
                const resposta = await fetch(urlComPagina, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!resposta.ok) return;

                const dados = await resposta.json();

                // paginaAtual = dados.current_page;

                paginaAtual = paginaAtual = new URLSearchParams(window.location.search).get('page') ?? '1';

                const ativos = dados.data.some(s =>
                    ['pendente', 'executando'].includes(s.status)
                );

                console.log(paginaAtual)

                botao.disabled = ativos;
                botao.innerHTML = ativos
                    ? '<span class="spinner-border spinner-border-sm me-1"></span> Sincronizando...'
                    : '<i class="icon ti ti-refresh"></i> Sincronizar';

                lista.replaceChildren();

                if (!dados.data.length) {
                    const coluna = document.createElement('div');
                    coluna.className = 'col-12';
                    coluna.textContent = 'Nenhuma sincronização realizada ainda.';
                    lista.appendChild(coluna);
                    return;
                }

                dados.data.forEach(s => {
                    const coluna = document.createElement('div');
                    coluna.className = 'col-md-6 col-xl-4';

                    const card = document.createElement('div');
                    card.className = 'card h-100 shadow-sm';

                    const corpo = document.createElement('div');
                    corpo.className = 'card-body';

                    const titulo = document.createElement('h5');
                    titulo.textContent = `Sincronização #${s.id}`;

                    const status = document.createElement('span');
                    status.className = 'badge ' + ({
                        concluida: 'bg-success',
                        falhou: 'bg-danger',
                        executando: 'bg-primary',
                        pendente: 'bg-secondary'
                    }[s.status] || 'bg-secondary');
                    status.textContent = s.status;

                    const etapa = document.createElement('p');
                    etapa.textContent = `Etapa: ${s.etapa_atual || '-'}`;

                    const inicio = document.createElement('p');
                    inicio.textContent = `Início: ${formatarData(s.iniciado_em) || 'Aguardando'}`;

                    const fim = document.createElement('p');
                    fim.textContent = `Término: ${formatarData(s.finalizado_em) || 'Em andamento'}`;

                    corpo.append(titulo, status, etapa, inicio, fim);

                    if (s.resultados) {
                        const subtitulo = document.createElement('strong');
                        subtitulo.textContent = 'Registros processados';
                        corpo.appendChild(subtitulo);

                        const ul = document.createElement('ul');

                        Object.entries(s.resultados).forEach(([nome, total]) => {
                            const li = document.createElement('li');
                            li.textContent = `${nome}: ${total}`;
                            ul.appendChild(li);
                        });

                        corpo.appendChild(ul);
                    }

                    if (s.erro) {
                        const erro = document.createElement('p');
                        erro.className = 'alert alert-danger mt-2';
                        erro.textContent = s.erro;
                        corpo.appendChild(erro);
                    }

                    card.appendChild(corpo);
                    coluna.appendChild(card);
                    lista.appendChild(coluna);
                });
            } catch (erro) {
                console.error('Erro ao consultar sincronizações:', erro);
            }
        }

        atualizarStatus();
        setInterval(atualizarStatus, 2000);
    });


    function formatarData(data) {
        if (!data) return null;

        const dataObj = new Date(data);

        if (isNaN(dataObj.getTime())) return data;

        return dataObj.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
    }
</script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
      const dropArea = document.getElementById("drop-area");
      const csvInput = document.getElementById("csv");
      const fileInfo = document.getElementById("file-info");

      const MAX_SIZE_MB = 10;

      function handleFile(file) {
        if (!file) return;

        // Verifica se é CSV
        if (!file.name.endsWith(".csv")) {
          fileInfo.textContent = "Por favor, selecione um arquivo CSV válido.";
          csvInput.value = "";
          return;
        }

        const sizeMB = file.size / (1024 * 1024);
        if (sizeMB > MAX_SIZE_MB) {
          fileInfo.textContent = "O arquivo ultrapassa 10MB.";
          csvInput.value = "";
          return;
        }

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        csvInput.files = dataTransfer.files;

        fileInfo.textContent = `Selecionado: ${file.name} (${sizeMB.toFixed(2)}MB)`;
      }

      dropArea.addEventListener("click", () => {
        csvInput.value = "";
        csvInput.click();
      });

      csvInput.addEventListener("change", () => {
        const file = csvInput.files[0];
        handleFile(file);
      });

      dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("dragover");
      });

      dropArea.addEventListener("dragleave", () => {
        dropArea.classList.remove("dragover");
      });

      dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("dragover");
        const file = e.dataTransfer.files[0];
        handleFile(file);
      });
    });
    
  document.addEventListener("DOMContentLoaded", function() {
      const dropArea = document.getElementById("drop-area-2");
      const csvInput = document.getElementById("csv-2");
      const fileInfo = document.getElementById("file-info-2");

      const MAX_SIZE_MB = 10;

      function handleFile(file) {
        if (!file) return;

        // Verifica se é CSV
        if (!file.name.endsWith(".csv")) {
          fileInfo.textContent = "Por favor, selecione um arquivo CSV válido.";
          csvInput.value = "";
          return;
        }

        const sizeMB = file.size / (1024 * 1024);
        if (sizeMB > MAX_SIZE_MB) {
          fileInfo.textContent = "O arquivo ultrapassa 10MB.";
          csvInput.value = "";
          return;
        }

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        csvInput.files = dataTransfer.files;

        fileInfo.textContent = `Selecionado: ${file.name} (${sizeMB.toFixed(2)}MB)`;
      }

      dropArea.addEventListener("click", () => {
        csvInput.value = "";
        csvInput.click();
      });

      csvInput.addEventListener("change", () => {
        const file = csvInput.files[0];
        handleFile(file);
      });

      dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("dragover");
      });

      dropArea.addEventListener("dragleave", () => {
        dropArea.classList.remove("dragover");
      });

      dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("dragover");
        const file = e.dataTransfer.files[0];
        handleFile(file);
      });
    });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const form1 = document.getElementById('modal-formimportar');
      const form2 = document.getElementById('modal-formimportar-membros');
      const overlay = document.getElementById('loading-overlay');

      form1.addEventListener('submit', function() {
        overlay.style.display = 'flex';
      });
      form2.addEventListener('submit', function() {
        overlay.style.display = 'flex';
      });
    });
</script>
@endsection