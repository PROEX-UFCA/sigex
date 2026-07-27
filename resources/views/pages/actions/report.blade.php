@extends('templates.template')

@section('styles')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
{{-- <link href="{{ asset('assets/libs/tom-select/dist/css/tom-select.bootstrap5.css') }}" rel="stylesheet" /> --}}
@endsection

@section('content')
<div class="page-body row">
  <div class="m-0 p-0 mb-4">
    <div
      class="col-12 p-0 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
      <div>
        <h3 class="mb-1 text-primary fw-bold">
          {{ $submissao->relatorio->titulo ?? 'Título não informado' }}
        </h3>
        <p class="mb-0 text-muted">
          Prazo: <span class="fw-bold text-dark">{{ date('d/m/Y',
            strtotime($submissao->relatorio->prazo))}}</span>
        </p>
      </div>
      <div>
        <a href="{{ route('actions.my') }}" class="btn d-inline-flex align-items-center">
          Voltar página
        </a>
      </div>
    </div>
    <div class="">
      <div class="row justify-content-center">
        <div class="">
          <div id="formWizard" data-submissao-id="{{ $submissao->id }}">
            @php
            $secaoAtivaIndex = 0;
            foreach ($submissao->relatorio->formulario->secoes as $i => $secao) {
                $secaoCompleta = true;
                foreach ($secao->perguntas as $p) {
                    // Ignora validação na aba pai se for tabela, validaremos depois se necessário
                    if ($p->obrigatoria && $p->tipo !== 'tabela') { 
                        $resp = $p->getRespostaPorSubmissao($submissao->id);
                        if (!$resp || empty($resp->valor ?? $resp->resposta ?? null)) {
                            $secaoCompleta = false;
                            break;
                        }
                    }
                }
                if (!$secaoCompleta) {
                    $secaoAtivaIndex = $i;
                    break;
                }
            }
            @endphp

            <ul class="nav nav-pills nav-justified mb-4" id="wizardTabs" role="tablist">
              @foreach ($submissao->relatorio->formulario->secoes as $index => $secao)
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ $index === $secaoAtivaIndex ? 'active' : '' }}" id="tab-btn-{{ $secao->id }}"
                  data-bs-toggle="pill" data-bs-target="#secao-{{ $secao->id }}" type="button" role="tab" {{ $index>
                  $secaoAtivaIndex ? 'disabled' : '' }}>
                  Seção {{ $index + 1 }}.
                </button>
              </li>
              @endforeach
            </ul>

            <div class="tab-content card shadow-sm p-4" id="wizardContent">
              @foreach ($submissao->relatorio->formulario->secoes as $index => $secao)
              <div class="tab-pane fade {{ $index === $secaoAtivaIndex ? 'show active' : '' }}"
                id="secao-{{ $secao->id }}" role="tabpanel">

                <h4 class="mb-1 text-primary">{{ $secao->titulo }}</h4>
                <p>{{ $secao->descricao }}</p>

                {{-- AQUI FILTRAMOS PARA MOSTRAR APENAS PERGUNTAS RAIZ (PAI) --}}
                @foreach ($secao->perguntas->whereNull('id_pergunta_pai') as $pergunta)

                @php
                $respostaModel = $pergunta->getRespostaPorSubmissao($submissao->id);
                $valorSalvo = $respostaModel->valor ?? $respostaModel->resposta ?? '';
                $arrayValores = is_string($valorSalvo) ? json_decode($valorSalvo, true) ?? [$valorSalvo] :
                (is_array($valorSalvo) ? $valorSalvo : [$valorSalvo]);
                @endphp

                <div class="mb-4">
                  
                  {{-- Oculta a label comum se for do tipo tabela, pois a tabela já tem seu cabeçalho --}}
                  @if($pergunta->tipo !== 'tabela')
                  <label class="form-label fw-bold">
                    {{ $pergunta->enunciado }}
                    @if($pergunta->obrigatoria)
                    <span class="text-danger" title="Campo obrigatório">*</span>
                    @endif
                  </label>
                  @endif

                  @switch($pergunta->tipo)

                  @case('text')
                  <input type="text" class="form-control form-salvar-estado" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria ? 'required' : '' }}
                  @if($pergunta->max && !$pergunta->regex) minlength="{{ $pergunta->max }}" @endif
                  @if($pergunta->max && !$pergunta->regex) maxlength="{{ $pergunta->max }}" @endif
                  @if($pergunta->regex) data-mascara="{{ $pergunta->regex }}" @endif
                  placeholder="Sua resposta aqui"
                  value="{{ $valorSalvo }}">

                  @if($pergunta->min || $pergunta->max || $pergunta->regex)
                  <div class="form-text text-muted">
                    @if($pergunta->max && !$pergunta->regex) Min: {{ $pergunta->min }} caracteres. @endif
                    @if($pergunta->max && !$pergunta->regex) Máx: {{ $pergunta->max }} caracteres. @endif
                    @if($pergunta->regex) Formato esperado: {{ $pergunta->regex }} @endif
                  </div>
                  @endif
                  @break

                  @case('location')
                  @php
                  $localData = is_string($valorSalvo) && json_decode($valorSalvo, true)
                  ? json_decode($valorSalvo, true)
                  : ['nome' => $valorSalvo, 'lat' => '', 'lng' => ''];

                  $localNome = $localData['nome'] ?? '';
                  $localLat = $localData['lat'] ?? '';
                  $localLng = $localData['lng'] ?? '';
                  @endphp

                  <div class="location-wrapper" id="location-wrapper-{{ $pergunta->id }}">
                    <div class="d-flex justify-content-between mb-2">
                      <span class="text-muted small">Pesquise pelo nome, CEP ou clique no mapa</span>
                      <a class="text-decoration-none small" onclick="toggleLocationInput('{{ $pergunta->id }}')"
                        href="javascript:void(0)">
                        Não encontrou seu local?
                      </a>
                    </div>

                    <div class="gap-2 mb-2" id="div-select-local-{{ $pergunta->id }}">
                      <select class="form-select select-location-tom form-salvar-estado"
                        id="select-local-{{ $pergunta->id }}" name="respostas[{{ $pergunta->id }}]"
                        data-map-id="{{ $pergunta->id }}" {{ $pergunta->obrigatoria ? 'required' : '' }}>
                        <option value="">Pesquisar no mapa...</option>
                        @if($localNome)
                        <option value="{{ $localNome }}" selected>{{ $localNome }}</option>
                        @endif
                      </select>
                    </div>

                    <div class="d-none mb-2" id="div-chose-{{ $pergunta->id }}">
                      <input type="text" class="form-control form-salvar-estado" id="input-local-{{ $pergunta->id }}"
                        placeholder="Digite o nome do local manualmente" value="{{ $localNome }}">
                      <div class="form-text text-danger">Modo de digitação manual ativo. O mapa será ignorado.</div>
                    </div>

                    <div id="map-{{ $pergunta->id }}" class="map-container border rounded"
                      style="height: 300px; width: 100%; z-index: 1;" data-saved-lat="{{ $localLat }}"
                      data-saved-lng="{{ $localLng }}"></div>

                    <input type="hidden" name="latitude[{{ $pergunta->id }}]" id="latitude-{{ $pergunta->id }}"
                      value="{{ $localLat }}">
                    <input type="hidden" name="longitude[{{ $pergunta->id }}]" id="longitude-{{ $pergunta->id }}"
                      value="{{ $localLng }}">
                  </div>
                  @break

                  @case('textarea')
                  <textarea class="form-control form-salvar-estado" name="respostas[{{ $pergunta->id }}]" rows="3" {{
                    $pergunta->obrigatoria ? 'required' : '' }} 
                    @if($pergunta->min) minlength="{{ $pergunta->min }}" @endif 
                    @if($pergunta->max) maxlength="{{ $pergunta->max }}" @endif 
                    placeholder="Digite sua resposta...">{{ $valorSalvo }}</textarea>

                  @if($pergunta->min || $pergunta->max)
                  <div class="form-text text-muted">
                    @if($pergunta->min) Mín: {{ $pergunta->min }} caracteres. @endif
                    @if($pergunta->max) Máx: {{ $pergunta->max }} caracteres. @endif
                  </div>
                  @endif
                  @break

                  @case('number')
                  <input type="number" class="form-control" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria ? 'required' : '' }}
                  @if($pergunta->min) min="{{ $pergunta->min }}" @endif
                  @if($pergunta->max) max="{{ $pergunta->max }}" @endif
                  @if($pergunta->step) step="{{ $pergunta->step }}" @endif
                  placeholder="Apenas números"
                  value="{{ $valorSalvo }}">

                  @if($pergunta->min || $pergunta->max || $pergunta->step)
                  <div class="form-text text-muted">
                    @if($pergunta->min) Valor mín: {{ $pergunta->min }}. @endif
                    @if($pergunta->max) Valor máx: {{ $pergunta->max }}. @endif
                    @if($pergunta->step) Intervalo numérico: {{ $pergunta->step }}. @endif
                  </div>
                  @endif
                  @break

                  @case('file')
                  <input type="file" class="form-control" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria && !$valorSalvo ? 'required' : '' }}
                  @if($pergunta->accept) accept="{{ $pergunta->accept }}" @endif>

                  @if($valorSalvo)
                  <div class="form-text text-success">
                    <i class="bi bi-check-circle"></i> Arquivo já enviado anteriormente. Envie outro apenas se desejar
                    substituir.
                  </div>
                  @endif

                  @if($pergunta->accept)
                  <div class="form-text text-muted">
                    Formatos aceitos: {{ str_replace(',', ', ', $pergunta->accept) }}
                  </div>
                  @endif
                  @break

                  @case('select')
                  <select class="form-select" name="respostas[{{ $pergunta->id }}]" {{ $pergunta->obrigatoria ?
                    'required' : '' }}>
                    <option value="" disabled {{ !$valorSalvo ? 'selected' : '' }}>Selecione uma opção</option>
                    @foreach($pergunta->opcoes as $opcao)
                    <option value="{{ $opcao->valor }}" {{ $valorSalvo==$opcao->valor ? 'selected' : '' }}>{{
                      $opcao->rotulo }}</option>
                    @endforeach
                  </select>
                  @break

                  @case('radio')
                  <div>
                    @foreach($pergunta->opcoes as $opcao)
                    <div class="form-check mb-1">
                      <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]"
                        id="opcao_{{ $opcao->id }}" value="{{ $opcao->valor }}" {{ $pergunta->obrigatoria ? 'required' :
                      '' }}
                      {{ $valorSalvo == $opcao->valor ? 'checked' : '' }}>
                      <label class="form-check-label" for="opcao_{{ $opcao->id }}">
                        {{ $opcao->rotulo }}
                      </label>
                    </div>
                    @endforeach
                  </div>
                  @break

                  @case('checkbox')
                  <div class="checkbox-group" @if($pergunta->obrigatoria) data-required="true" @endif>
                    @foreach($pergunta->opcoes as $opcao)
                    <div class="form-check mb-1">
                      <input class="form-check-input" type="checkbox" name="respostas[{{ $pergunta->id }}][]"
                        id="opcao_{{ $opcao->id }}" value="{{ $opcao->valor }}" {{ in_array($opcao->valor,
                      $arrayValores) ? 'checked' : '' }}>
                      <label class="form-check-label" for="opcao_{{ $opcao->id }}">
                        {{ $opcao->rotulo }}
                      </label>
                    </div>
                    @endforeach
                  </div>
                  @break

                  @case('date')
                  @case('datetime-local')
                  <input type="{{ $pergunta->tipo }}" class="form-control" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria ? 'required' : '' }}
                  @if($pergunta->min) min="{{ $pergunta->min }}" @endif
                  @if($pergunta->max) max="{{ $pergunta->max }}" @endif
                  value="{{ $valorSalvo }}">

                  @if($pergunta->min || $pergunta->max)
                  <div class="form-text text-muted">
                    @if($pergunta->min) Permitido a partir de: {{ $pergunta->min }}. @endif
                    @if($pergunta->max) Permitido até: {{ $pergunta->max }}. @endif
                  </div>
                  @endif
                  @break

                  {{-- --- INÍCIO DA LÓGICA DE TABELA / REPEATER --- --}}
                  @case('tabela')
                  @php
                      $respostasTabela = \App\Models\Resposta::whereIn('id_pergunta', $pergunta->filhas->pluck('id'))
                                          ->where('id_submissao', $submissao->id)
                                          ->get()
                                          ->groupBy('indice_grupo');
                      
                      if($respostasTabela->isEmpty()) {
                          $respostasTabela->put(0, collect()); 
                      }
                  @endphp

                  <div class="repeater-container p-3 border border-primary border-opacity-25 rounded bg-light mb-3" id="tabela-{{ $pergunta->id }}">
                    <div class="d-flex align-items-center mb-2">
                        <label class="form-label fw-bold mb-0 text-primary fs-4">{{ $pergunta->enunciado }}</label>
                    </div>
                    <div class="d-flex align-items-center mb-3 text-muted" style="font-size: 0.85rem;">
                      <i class="ti ti-layers-linked me-2"></i> 
                      <span>Preencha os itens abaixo (Você pode adicionar quantos precisar)</span>
                    </div>

                    <div class="repeater-linhas" id="linhas-{{ $pergunta->id }}">
                      @foreach($respostasTabela as $indice => $respostasLinha)
                        <div class="card shadow-sm mb-3 linha-item border-0" data-indice="{{ $indice }}" id="linha-{{ $pergunta->id }}-{{ $indice }}">
                          <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="fw-bold text-muted small">Item #<span class="numero-item">{{ $loop->iteration }}</span></span>
                            
                            <button type="button" class="btn btn-sm btn-outline-danger py-1" onclick="removerLinhaTabela('{{ $pergunta->id }}', '{{ $indice }}')">
                              <i class="ti ti-trash"></i> Remover
                            </button>
                          </div>
                          
                          <div class="card-body p-3">
                            <div class="row g-3">
                              @php
                                $qtdColunas = $pergunta->filhas->count();
                                $gridClass = $qtdColunas > 3 ? 'col-12 col-md-6 col-lg-4' : 'col-12 col-md';
                              @endphp

                              @foreach($pergunta->filhas as $coluna)
                                @php
                                  $respColuna = $respostasLinha->where('id_pergunta', $coluna->id)->first();
                                  $valorSalvoCol = $respColuna->valor ?? $respColuna->resposta ?? '';
                                  $arrayValoresCol = is_string($valorSalvoCol) ? json_decode($valorSalvoCol, true) ?? [$valorSalvoCol] : (is_array($valorSalvoCol) ? $valorSalvoCol : [$valorSalvoCol]);
                                  
                                  $inputUnicoId = $coluna->id . '-' . $indice;
                                @endphp
                                
                                <div class="{{ $gridClass }}">
                                  <label class="form-label fw-semibold mb-1" style="font-size: 0.85rem;">
                                    {{ $coluna->enunciado }} @if($coluna->obrigatoria) <span class="text-danger">*</span> @endif
                                  </label>

                                  @switch($coluna->tipo)
                                    @case('text')
                                      <input type="text" class="form-control form-control-sm form-salvar-estado" name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" value="{{ $valorSalvoCol }}" {{ $coluna->obrigatoria ? 'required' : '' }} @if($coluna->max && !$coluna->regex) maxlength="{{ $coluna->max }}" @endif @if($coluna->regex) data-mascara="{{ $coluna->regex }}" @endif placeholder="Sua resposta aqui">
                                      @break
                                    
                                    @case('textarea')
                                      <textarea class="form-control form-control-sm form-salvar-estado" name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" rows="2" {{ $coluna->obrigatoria ? 'required' : '' }}>{{ $valorSalvoCol }}</textarea>
                                      @break
                                      
                                    @case('number')
                                      <input type="number" class="form-control form-control-sm form-salvar-estado" name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" value="{{ $valorSalvoCol }}" {{ $coluna->obrigatoria ? 'required' : '' }} @if($coluna->min) min="{{ $coluna->min }}" @endif @if($coluna->max) max="{{ $coluna->max }}" @endif @if($coluna->step) step="{{ $coluna->step }}" @endif>
                                      @break
                                      
                                    @case('file')
                                      <input type="file" class="form-control form-control-sm form-salvar-estado" name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" {{ $coluna->obrigatoria && !$valorSalvoCol ? 'required' : '' }} @if($coluna->accept) accept="{{ $coluna->accept }}" @endif>
                                      @if($valorSalvoCol)
                                        <div class="form-text text-success" style="font-size: 0.7rem;"><i class="bi bi-check-circle"></i> Arquivo já enviado.</div>
                                      @endif
                                      @break
                                      
                                    @case('select')
                                      <select class="form-select form-select-sm form-salvar-estado" name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" {{ $coluna->obrigatoria ? 'required' : '' }}>
                                        <option value="" disabled {{ !$valorSalvoCol ? 'selected' : '' }}>Selecione</option>
                                        @foreach($coluna->opcoes as $op)
                                          <option value="{{ $op->valor }}" {{ $valorSalvoCol == $op->valor ? 'selected' : '' }}>{{ $op->rotulo }}</option>
                                        @endforeach
                                      </select>
                                      @break
                                      
                                    @case('radio')
                                      <div>
                                        @foreach($coluna->opcoes as $op)
                                          <div class="form-check mb-1">
                                            <input class="form-check-input form-salvar-estado" type="radio" name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" id="opcao_{{ $op->id }}_{{ $indice }}" value="{{ $op->valor }}" {{ $coluna->obrigatoria ? 'required' : '' }} {{ $valorSalvoCol == $op->valor ? 'checked' : '' }}>
                                            <label class="form-check-label" style="font-size: 0.85rem;" for="opcao_{{ $op->id }}_{{ $indice }}">{{ $op->rotulo }}</label>
                                          </div>
                                        @endforeach
                                      </div>
                                      @break

                                    @case('checkbox')
                                      <div>
                                        @foreach($coluna->opcoes as $op)
                                          <div class="form-check mb-1">
                                            <input class="form-check-input form-salvar-estado" type="checkbox" name="respostas[{{ $coluna->id }}][]" data-indice="{{ $indice }}" id="opcao_{{ $op->id }}_{{ $indice }}" value="{{ $op->valor }}" {{ in_array($op->valor, $arrayValoresCol) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="font-size: 0.85rem;" for="opcao_{{ $op->id }}_{{ $indice }}">{{ $op->rotulo }}</label>
                                          </div>
                                        @endforeach
                                      </div>
                                      @break
                                      
                                    @case('date')
                                    @case('datetime-local')
                                      <input type="{{ $coluna->tipo }}" class="form-control form-control-sm form-salvar-estado" name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" value="{{ $valorSalvoCol }}" {{ $coluna->obrigatoria ? 'required' : '' }}>
                                      @break
                                      
                                    @case('location')
                                      @php
                                        $localDataCol = is_string($valorSalvoCol) && json_decode($valorSalvoCol, true) ? json_decode($valorSalvoCol, true) : ['nome' => $valorSalvoCol, 'lat' => '', 'lng' => ''];
                                        $localNomeCol = $localDataCol['nome'] ?? '';
                                        $localLatCol = $localDataCol['lat'] ?? '';
                                        $localLngCol = $localDataCol['lng'] ?? '';
                                      @endphp
                                      <div class="location-wrapper" id="location-wrapper-{{ $inputUnicoId }}">
                                        <div class="d-flex justify-content-between mb-1">
                                          <span class="text-muted small" style="font-size: 0.7rem;">Pesquise no mapa</span>
                                          <a class="text-decoration-none small" style="font-size: 0.7rem;" onclick="toggleLocationInput('{{ $inputUnicoId }}')" href="javascript:void(0)">Manual?</a>
                                        </div>

                                        <div class="gap-2 mb-2" id="div-select-local-{{ $inputUnicoId }}">
                                          <select class="form-select select-location-tom form-salvar-estado" id="select-local-{{ $inputUnicoId }}" name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" data-map-id="{{ $inputUnicoId }}" {{ $coluna->obrigatoria ? 'required' : '' }}>
                                            <option value="">Pesquisar...</option>
                                            @if($localNomeCol)
                                              <option value="{{ $localNomeCol }}" selected>{{ $localNomeCol }}</option>
                                            @endif
                                          </select>
                                        </div>

                                        <div class="d-none mb-2" id="div-chose-{{ $inputUnicoId }}">
                                          <input type="text" class="form-control form-control-sm form-salvar-estado" id="input-local-{{ $inputUnicoId }}" data-indice="{{ $indice }}" placeholder="Nome do local" value="{{ $localNomeCol }}">
                                        </div>

                                        <div id="map-{{ $inputUnicoId }}" class="map-container border rounded" style="height: 150px; width: 100%; z-index: 1;" data-saved-lat="{{ $localLatCol }}" data-saved-lng="{{ $localLngCol }}"></div>

                                        <input type="hidden" class="form-salvar-estado" name="latitude[{{ $coluna->id }}]" data-indice="{{ $indice }}" id="latitude-{{ $inputUnicoId }}" value="{{ $localLatCol }}">
                                        <input type="hidden" class="form-salvar-estado" name="longitude[{{ $coluna->id }}]" data-indice="{{ $indice }}" id="longitude-{{ $inputUnicoId }}" value="{{ $localLngCol }}">
                                      </div>
                                      @break
                                  @endswitch
                                  
                                  @if($coluna->min || $coluna->max || $coluna->step || $coluna->accept || $coluna->regex)
                                    <div class="form-text text-muted" style="font-size: 0.65rem;">
                                      @if($coluna->tipo == 'number')
                                        @if($coluna->min) Mín: {{ $coluna->min }}. @endif
                                        @if($coluna->max) Máx: {{ $coluna->max }}. @endif
                                      @elseif($coluna->tipo == 'file')
                                        {{ str_replace(',', ', ', $coluna->accept) }}
                                      @else
                                        @if($coluna->min) Mín: {{ $coluna->min }}. @endif
                                        @if($coluna->max) Máx: {{ $coluna->max }}. @endif
                                      @endif
                                    </div>
                                  @endif
                                </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>

                    <button type="button" class="btn btn-primary btn-sm mt-2" onclick="adicionarLinhaTabela('{{ $pergunta->id }}')">
                      <i class="ti ti-plus"></i> Adicionar Novo Item
                    </button>
                  </div>
                  @break
                  {{-- --- FIM DA LÓGICA DE TABELA / REPEATER --- --}}

                  @endswitch
                </div>
                @endforeach

                <div class="d-flex justify-content-between mt-5 border-top pt-3">
                  @if ($index > 0)
                  <button type="button" class="btn btn-outline-secondary btn-anterior">
                    <i class="bi bi-arrow-left"></i> Anterior
                  </button>
                  @else
                  <div></div>
                  @endif

                  @if (!$loop->last)
                  <button type="button" class="btn btn-primary btn-proximo">
                    Próximo Passo <i class="bi bi-arrow-right"></i>
                  </button>
                  @else
                  <form id="finalizar" action="{{ route('report.finish', $submissao->id) }}" method="POST"
                    onsubmit="return confirm('Deseja realmente finalizar esse relatório?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="btn-finalizar" for="finalizar"
                      class="btn btn-success {{ $progresso == 100 ? '' : 'd-none'}}">
                      Finalizar e Enviar
                    </button>
                  </form>
                  @endif
                </div>
              </div>
              @endforeach
            </div>
          </div>

          <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
            <div class="toast" id="toast-autosave" role="alert" aria-live="assertive" aria-atomic="true"
              data-bs-delay="3000">
              <div class="toast-header">
                <span
                  class="avatar avatar-xs me-2 bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-check"></i>
                </span>
                <strong class="me-auto" id="toast-title">Salvamento Automático</strong>
                <small>agora mesmo</small>
                <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
              <div class="toast-body" id="toast-message">
                Progresso salvo com sucesso!
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="{{ asset('assets/libs/tom-select/dist/js/tom-select.base.min.js') }}"></script>
<script src="{{ asset('assets/js/report.js') }}"></script>
<script>
  $(document).ready(function() {
    // --- LÓGICA DE MÁSCARAS DINÂMICAS ---
    $('input[data-mascara]').each(function() {
        let formatoMascara = $(this).attr('data-mascara');
        $(this).mask(formatoMascara);
    });
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });

    $('#formWizard').on('change', 'input, select, textarea', function() {
        let input = $(this);
        let nameAttribute = input.attr('name');
        
        if(!nameAttribute || nameAttribute.indexOf('respostas[') === -1) return;

        let match = nameAttribute.match(/respostas\[(.*?)\]/);
        if (!match) return;
        
        let id_pergunta = match[1];
        let id_submissao = $('#formWizard').data('submissao-id');
        
        let formData = new FormData();
        formData.append('id_submissao', id_submissao);
        formData.append('id_pergunta', id_pergunta);

        // NOVA LÓGICA DO ÍNDICE DA TABELA
        let indice_grupo = input.attr('data-indice');
        if (typeof indice_grupo !== 'undefined' && indice_grupo !== false) {
            formData.append('indice_grupo', indice_grupo);
        } else {
            formData.append('indice_grupo', 0); 
        }

        if (input.is(':checkbox')) {
            let nomeSeguro = nameAttribute.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
            
            if (typeof indice_grupo !== 'undefined' && indice_grupo !== false) {
                $(`input[name="${nomeSeguro}"][data-indice="${indice_grupo}"]:checked`).each(function() {
                    formData.append('valor[]', $(this).val());
                });
                if ($(`input[name="${nomeSeguro}"][data-indice="${indice_grupo}"]:checked`).length === 0) {
                    formData.append('valor', '');
                }
            } else {
                $(`input[name="${nomeSeguro}"]:checked`).each(function() {
                    formData.append('valor[]', $(this).val());
                });
                if ($(`input[name="${nomeSeguro}"]:checked`).length === 0) {
                    formData.append('valor', '');
                }
            }
        }
        else if (input.is(':file')) {
            if (input[0].files.length > 0) {
                formData.append('file', input[0].files[0]);
            } else {
                return; 
            }
        }
        else if (input.hasClass('select-location-tom') || (input.attr('id') && input.attr('id').startsWith('input-local-'))) {
            let mapIdSufix = input.attr('data-map-id') || input.attr('id').replace('input-local-', '');
            
            let lat = $('#latitude-' + mapIdSufix).val();
            let lng = $('#longitude-' + mapIdSufix).val();
            let nomeLocal = input.val();

            let jsonLocation = JSON.stringify({
                nome: nomeLocal,
                lat: lat,
                lng: lng
            });
            
            formData.append('valor', jsonLocation);
        }
        else {
            formData.append('valor', input.val());
        }

        $.ajax({
            url: "{{ route('respostas.autosave') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                exibirToast('Progresso salvo!', response.message, 'success');

                $('#barra-progresso').css('width', response.progresso + '%');

                if (response.progresso >= 100) {
                    $('#btn-finalizar').show();
                    $('#btn-finalizar').removeClass('d-none');
                  } else {
                    $('#btn-finalizar').hide();
                    $('#btn-finalizar').removeClass('d-none');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                exibirToast('Erro ao salvar', 'Ocorreu um erro ao salvar esta resposta.', 'danger');
            }
        });
    });

    function exibirToast(titulo, mensagem, tipo) {
        let toastEl = $('#toast-autosave');
        let icone = toastEl.find('.avatar');
        icone.removeClass('bg-success bg-danger').addClass('bg-' + tipo);
        
        if (tipo === 'success') {
            icone.html('<i class="bi bi-check"></i>');
        } else {
            icone.html('<i class="bi bi-x"></i>');
        }

        toastEl.find('#toast-title').text(titulo);
        toastEl.find('#toast-message').text(mensagem);
        
        let toast = new bootstrap.Toast(toastEl[0]);
        toast.show();
    }
  });
</script>
@endsection