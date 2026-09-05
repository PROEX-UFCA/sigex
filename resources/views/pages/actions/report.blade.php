@extends('templates.template')

@section('styles')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
{{--
<link href="{{ asset('assets/libs/tom-select/dist/css/tom-select.bootstrap5.css') }}" rel="stylesheet" /> --}}
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

            {{-- 1. RENDERIZAÇÃO DAS ABAS (TABS) COM INDICADOR DE ERRO --}}
            <ul class="nav nav-pills nav-justified mb-4" id="wizardTabs" role="tablist">
              @foreach ($submissao->relatorio->formulario->secoes as $index => $secao)
              @php
              // Lógica para verificar se existe erro nesta seção para colocar o ícone
              $temErroNaSecao = false;
              foreach ($secao->perguntas as $p) {
              if ($p->tipo === 'tabela') {
              $respostasTabelaSecao = \App\Models\Resposta::whereIn('id_pergunta',
              $p->filhas->pluck('id'))->where('id_submissao', $submissao->id)->get();
              foreach($respostasTabelaSecao as $rt) {
              if ($rt->validacao && $rt->validacao->status == 0) { $temErroNaSecao = true; break; }
              }
              } else {
              $resp = $p->getRespostaPorSubmissao($submissao->id);
              if ($resp && $resp->validacao && $resp->validacao->status == 0) { $temErroNaSecao = true; break; }
              }
              if($temErroNaSecao) break;
              }
              @endphp

              <li class="nav-item" role="presentation">
                <button
                  class="nav-link {{ $index === 0 ? 'active' : '' }} d-flex align-items-center justify-content-center gap-2"
                  id="tab-btn-{{ $secao->id }}" data-bs-toggle="pill" data-bs-target="#secao-{{ $secao->id }}"
                  type="button" role="tab">
                  Seção {{ $index + 1 }}
                  @if($temErroNaSecao)
                  <i class="bi bi-exclamation-circle-fill text-danger"
                    title="Esta seção contém correções pendentes"></i>
                  @endif
                </button>
              </li>
              @endforeach
            </ul>

            {{-- 2. CONTEÚDO DAS SEÇÕES --}}
            <div class="tab-content card shadow-sm p-4" id="wizardContent">
              @foreach ($submissao->relatorio->formulario->secoes as $index => $secao)
              <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="secao-{{ $secao->id }}"
                role="tabpanel">

                <h4 class="mb-1 text-primary">{{ $secao->titulo }}</h4>
                <p>{{ $secao->descricao }}</p>

                @foreach ($secao->perguntas->whereNull('id_pergunta_pai') as $pergunta)

                @php
                $respostaModel = $pergunta->getRespostaPorSubmissao($submissao->id);
                $valorSalvo = $respostaModel->valor ?? $respostaModel->resposta ?? '';
                $arrayValores = is_string($valorSalvo) ? json_decode($valorSalvo, true) ?? [$valorSalvo] :
                (is_array($valorSalvo) ? $valorSalvo : [$valorSalvo]);

                // VARIÁVEIS DE AVALIAÇÃO (PERGUNTA NORMAL)
                $validacao = $respostaModel ? $respostaModel->validacao : null;
                $bloqueado = $validacao && $validacao->status == 1; // Só bloqueia se for 1 (Aprovado)
                $precisaCorrigir = $validacao && $validacao->status == 0;
                @endphp

                <div
                  class="mb-4 p-3 rounded {{ $precisaCorrigir ? 'border border-danger border-2 bg-danger-lt bg-danger-subtle' : '' }}">

                  @if($pergunta->tipo !== 'tabela')
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-bold mb-0 {{ $precisaCorrigir ? 'text-danger' : '' }}">
                      @if($precisaCorrigir) <i class="bi bi-exclamation-triangle-fill me-1"></i> @endif
                      @if($bloqueado) <i class="bi bi-check-circle-fill text-success me-1"></i> @endif
                      {{ $pergunta->enunciado }}
                      @if($pergunta->obrigatoria) <span class="text-danger" title="Campo obrigatório">*</span> @endif
                    </label>
                    @if($bloqueado) <span class="badge bg-success">Aprovado</span> @endif
                  </div>

                  @if($precisaCorrigir)
                  <div class="alert alert-danger py-2 px-3 mb-3 small fw-bold">
                    <i class="bi bi-chat-left-text me-1"></i> Motivo da Correção: {{ $validacao->correcao }}
                  </div>
                  @endif
                  @endif

                  @switch($pergunta->tipo)

                  {{-- TIPOS NORMAIS --}}
                  @case('text')
                  <input type="text" class="form-control form-salvar-estado" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria ? 'required' : '' }} {{ $bloqueado ? 'disabled' : '' }} @if($pergunta->regex)
                  data-mascara="{{ $pergunta->regex }}" @endif
                  placeholder="Sua resposta aqui" value="{{ $valorSalvo}}" @if($pergunta->min) minlength="{{
                  $pergunta->min }}" @endif @if($pergunta->max) maxlength="{{ $pergunta->max }}" @endif>
                  <div class="form-text text-muted">
                    @if($pergunta->min) Mín: {{ $pergunta->min }} caracteres. @endif
                    @if($pergunta->max) Máx: {{ $pergunta->max }} caracteres. @endif
                    @if($pergunta->regex) <span title="{{ $pergunta->regex }}">Formato: {{ $pergunta->regex }}.</span>
                    @endif
                  </div>
                  @break

                  @case('textarea')
                  <textarea class="form-control form-salvar-estado" name="respostas[{{ $pergunta->id }}]" rows="3"
                    {{$pergunta->obrigatoria ? 'required' : '' }} {{ $bloqueado ? 'disabled' : '' }} @if($pergunta->min) minlength="{{ $pergunta->min }}" @endif @if($pergunta->max) maxlength="{{ $pergunta->max }}" @endif @if($pergunta->regex) data-mascara="{{ $pergunta->regex }}" @endif>{{ $valorSalvo }}</textarea>
                  <div class="form-text text-muted">
                    @if($pergunta->min) Mín: {{ $pergunta->min }} caracteres. @endif
                    @if($pergunta->max) Máx: {{ $pergunta->max }} caracteres. @endif
                    @if($pergunta->regex) <span title="{{ $pergunta->regex }}">Formato: {{ $pergunta->regex }}.</span>
                    @endif
                  </div>
                  @break

                  @case('number')
                  <input type="number" class="form-control form-salvar-estado" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria ? 'required' : '' }} {{ $bloqueado ? 'disabled' : '' }} value="{{ $valorSalvo
                  }}" @if($pergunta->min) min="{{ $pergunta->min }}" @endif @if($pergunta->max) max="{{ $pergunta->max
                  }}" @endif @if($pergunta->step) step="{{ $pergunta->step }}" @endif>
                  <div class="form-text text-muted">
                    @if($pergunta->min) Mín: {{ $pergunta->min }}. @endif
                    @if($pergunta->max) Máx: {{ $pergunta->max }}. @endif
                    @if($pergunta->step) <span title="{{ $pergunta->step }}">Intervalo: {{ $pergunta->step }}.</span>
                    @endif
                  </div>
                  @break

                  @case('date')
                  @case('datetime-local')
                  <input type="{{ $pergunta->tipo }}" class="form-control form-salvar-estado"
                    name="respostas[{{ $pergunta->id }}]" {{ $pergunta->obrigatoria ? 'required' : '' }} {{ $bloqueado ?
                  'disabled' : '' }} value="{{ $valorSalvo }}">
                  @break

                  @case('select')
                  <select class="form-select form-salvar-estado" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria ? 'required' : '' }} {{ $bloqueado ? 'disabled' : '' }}>
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
                      <input class="form-check-input form-salvar-estado" type="radio"
                        name="respostas[{{ $pergunta->id }}]" id="opcao_{{ $opcao->id }}" value="{{ $opcao->valor }}" {{
                        $pergunta->obrigatoria ? 'required' : '' }} {{ $valorSalvo == $opcao->valor ? 'checked' : '' }}
                      {{ $bloqueado ? 'disabled' : '' }}>
                      <label class="form-check-label" for="opcao_{{ $opcao->id }}">{{ $opcao->rotulo }}</label>
                    </div>
                    @endforeach
                  </div>
                  @break

                  @case('checkbox')
                  <div class="checkbox-group">
                    @foreach($pergunta->opcoes as $opcao)
                    <div class="form-check mb-1">
                      <input class="form-check-input form-salvar-estado" type="checkbox"
                        name="respostas[{{ $pergunta->id }}][]" id="opcao_{{ $opcao->id }}" value="{{ $opcao->valor }}"
                        {{ in_array($opcao->valor, $arrayValores) ? 'checked' : '' }} {{ $bloqueado ? 'disabled' : ''
                      }}>
                      <label class="form-check-label" for="opcao_{{ $opcao->id }}">{{ $opcao->rotulo }}</label>
                    </div>
                    @endforeach
                  </div>
                  @break

                  @case('file')
                  <input type="file" class="form-control form-salvar-estado" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria && !$valorSalvo ? 'required' : '' }} {{ $bloqueado ? 'disabled' : '' }}
                  @if($pergunta->accept) accept="{{ $pergunta->accept }}" @endif>
                  @if($pergunta->accept)
                  <div class="form-text text-muted">
                    Formatos aceitos: {{ str_replace(',', ', ', $pergunta->accept) }}
                  </div>
                  @endif
                  @if($valorSalvo)
                  <div class="form-text text-success mt-1"><i class="bi bi-check-circle"></i> Arquivo já enviado. Envie
                    outro para substituir.</div>
                  @endif
                  @break

                  @case('location')
                  @php
                  $localData = is_string($valorSalvo) && json_decode($valorSalvo, true) ? json_decode($valorSalvo, true)
                  : ['nome' => $valorSalvo, 'lat' => '', 'lng' => ''];
                  $localNome = $localData['nome'] ?? '';
                  $localLat = $localData['lat'] ?? '';
                  $localLng = $localData['lng'] ?? '';
                  @endphp
                  <div class="location-wrapper position-relative" id="location-wrapper-{{ $pergunta->id }}">
                    @if($bloqueado)
                    <div class="position-absolute w-100 h-100 bg-white bg-opacity-50"
                      style="z-index: 10; top:0; left:0;"></div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                      <span class="text-muted small">Pesquise pelo nome ou clique no mapa</span>
                      <a class="text-decoration-none small" onclick="toggleLocationInput('{{ $pergunta->id }}')"
                        href="javascript:void(0)">Não encontrou?</a>
                    </div>
                    <div class="gap-2 mb-2" id="div-select-local-{{ $pergunta->id }}">
                      <select class="form-select select-location-tom form-salvar-estado"
                        id="select-local-{{ $pergunta->id }}" name="respostas[{{ $pergunta->id }}]"
                        data-map-id="{{ $pergunta->id }}" {{ $pergunta->obrigatoria ? 'required' : '' }} {{ $bloqueado ?
                        'disabled' : '' }}>
                        <option value="">Pesquisar no mapa...</option>
                        @if($localNome) <option value="{{ $localNome }}" selected>{{ $localNome }}</option> @endif
                      </select>
                    </div>
                    <div class="d-none mb-2" id="div-chose-{{ $pergunta->id }}">
                      <input type="text" class="form-control form-salvar-estado" id="input-local-{{ $pergunta->id }}"
                        placeholder="Digite o local manualmente" value="{{ $localNome }}" {{ $bloqueado ? 'disabled'
                        : '' }}>
                    </div>
                    <div id="map-{{ $pergunta->id }}" class="map-container border rounded"
                      style="height: 250px; width: 100%; z-index: 1;" data-saved-lat="{{ $localLat }}"
                      data-saved-lng="{{ $localLng }}"></div>
                    <input type="hidden" name="latitude[{{ $pergunta->id }}]" id="latitude-{{ $pergunta->id }}"
                      value="{{ $localLat }}" {{ $bloqueado ? 'disabled' : '' }}>
                    <input type="hidden" name="longitude[{{ $pergunta->id }}]" id="longitude-{{ $pergunta->id }}"
                      value="{{ $localLng }}" {{ $bloqueado ? 'disabled' : '' }}>
                  </div>
                  @break


                  {{-- ========================================== --}}
                  {{-- INÍCIO DA LÓGICA DE TABELA / REPEATER --}}
                  {{-- ========================================== --}}
                  @case('tabela')
                  @php
                  $respostasTabela = \App\Models\Resposta::whereIn('id_pergunta',
                  $pergunta->filhas->pluck('id'))->where('id_submissao',
                  $submissao->id)->get()->groupBy('indice_grupo');
                  if($respostasTabela->isEmpty()) { $respostasTabela->put(0, collect()); }
                  @endphp

                  <div class="repeater-container p-3 border border-primary border-opacity-25 rounded bg-white mb-3"
                    id="tabela-{{ $pergunta->id }}">
                    <div class="d-flex align-items-center mb-3">
                      <label
                        class="form-label fw-bold mb-0 text-primary fs-5 {{ $pergunta->obrigatoria == 1 ? 'required' : '' }}">{{
                        $pergunta->enunciado }}</label>
                    </div>

                    <div class="repeater-linhas" id="linhas-{{ $pergunta->id }}">
                      @foreach($respostasTabela as $indice => $respostasLinha)

                      {{-- Verifica se a linha tem algum campo aprovado para impedir exclusão --}}
                      @php
                      $linhaTemAprovado = false;
                      foreach($pergunta->filhas as $c) {
                      $r = $respostasLinha->where('id_pergunta', $c->id)->first();
                      if($r && $r->validacao && $r->validacao->status == 1) $linhaTemAprovado = true;
                      }
                      @endphp

                      <div class="card shadow-sm mb-3 linha-item border" data-indice="{{ $indice }}"
                        id="linha-{{ $pergunta->id }}-{{ $indice }}">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                          <span class="fw-bold text-muted small">Item #<span class="numero-item">{{ $loop->iteration
                              }}</span></span>
                          @if(!$linhaTemAprovado)
                          <button type="button" class="btn btn-sm btn-outline-danger py-1"
                            onclick="removerLinhaTabela('{{ $pergunta->id }}', '{{ $indice }}')">
                            <i class="ti ti-trash"></i> Remover
                          </button>
                          @endif
                        </div>

                        <div class="card-body p-3">
                          <div class="row g-3">
                            @foreach($pergunta->filhas as $coluna)
                            @php
                            $respColuna = $respostasLinha->where('id_pergunta', $coluna->id)->first();
                            $valorSalvoCol = $respColuna->valor ?? $respColuna->resposta ?? '';
                            $arrayValoresCol = is_string($valorSalvoCol) ? json_decode($valorSalvoCol, true) ??
                            [$valorSalvoCol] : (is_array($valorSalvoCol) ? $valorSalvoCol : [$valorSalvoCol]);
                            $inputUnicoId = $coluna->id . '-' . $indice;

                            $validacaoCol = $respColuna ? $respColuna->validacao : null;
                            $bloqueadoCol = $validacaoCol && $validacaoCol->status == 1;
                            $precisaCorrigirCol = $validacaoCol && $validacaoCol->status == 0;
                            @endphp

                            <div
                              class="col-12 col-md {{ $precisaCorrigirCol ? 'border border-danger rounded bg-danger-lt bg-danger-subtle p-2' : '' }}">
                              <label class="form-label fw-semibold mb-1 {{ $precisaCorrigirCol ? 'text-danger' : '' }}"
                                style="font-size: 0.85rem;">
                                @if($precisaCorrigirCol) <i class="bi bi-exclamation-triangle-fill"></i> @endif
                                @if($bloqueadoCol) <i class="bi bi-check-circle-fill text-success"></i> @endif
                                {{ $coluna->enunciado }} @if($coluna->obrigatoria) <span class="text-danger">*</span>
                                @endif
                              </label>

                              @if($precisaCorrigirCol)
                              <div class="text-danger small mb-2 fw-bold" style="font-size: 0.75rem;">
                                Motivo da correção: {{ $validacaoCol->correcao }}
                              </div>
                              @endif

                              @switch($coluna->tipo)

                              @case('text')
                              <input type="text" class="form-control form-control-sm form-salvar-estado"
                                name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}"
                                value="{{ $valorSalvoCol }}" {{ $coluna->obrigatoria ? 'required' : '' }} {{
                              $bloqueadoCol ? 'disabled' : '' }} @if($coluna->min) minlength="{{ $coluna->min }}"
                              @endif @if($coluna->max) maxlength="{{ $coluna->max }}" @endif @if($coluna->regex)
                              data-mascara="{{ $coluna->regex }}" @endif>
                              @if($coluna->min || $coluna->max || $coluna->regex)
                              <div class="form-text text-muted">
                                @if($coluna->min) Mín: {{ $coluna->min }} caracteres. @endif
                                @if($coluna->max) Máx: {{ $coluna->max }} caracteres. @endif
                                @if($coluna->regex) <span title="{{ $coluna->regex }}">Formato: {{ $coluna->regex
                                  }}.</span> @endif
                              </div>
                              @endif
                              @break

                              @case('textarea')
                              <textarea class="form-control form-control-sm form-salvar-estado"
                                name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" rows="2" {{ $coluna->obrigatoria ? 'required' : '' }} {{ $bloqueadoCol ? 'disabled' : '' }} @if($coluna->min) minlength="{{ $coluna->min }}"
                              @endif @if($coluna->max) maxlength="{{ $coluna->max }}" @endif @if($coluna->regex)
                              data-mascara="{{ $coluna->regex }}" @endif>{{ $valorSalvoCol }}</textarea>
                              @if($coluna->min || $coluna->max || $coluna->regex)
                              <div class="form-text text-muted">
                                @if($coluna->min) Mín: {{ $coluna->min }} caracteres. @endif
                                @if($coluna->max) Máx: {{ $coluna->max }} caracteres. @endif
                                @if($coluna->regex) <span title="{{ $coluna->regex }}">Formato: {{ $coluna->regex
                                  }}.</span> @endif
                              </div>
                              @endif
                              @break

                              @case('number')
                              <input type="number" class="form-control form-control-sm form-salvar-estado"
                                name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}"
                                value="{{ $valorSalvoCol }}" {{ $coluna->obrigatoria ? 'required' : '' }} {{
                              $bloqueadoCol ? 'disabled' : '' }} @if($coluna->min) min="{{ $coluna->min }}" @endif
                              @if($coluna->max) max="{{ $coluna->max
                              }}" @endif @if($coluna->step) step="{{ $coluna->step }}" @endif>
                              <div class="form-text text-muted">
                                @if($coluna->min) Mín: {{ $coluna->min }}. @endif
                                @if($coluna->max) Máx: {{ $coluna->max }}. @endif
                                @if($coluna->step) <span title="{{ $coluna->step }}">Intervalo: {{ $coluna->step
                                  }}.</span>
                                @endif
                              </div>
                              @break

                              @case('date')
                              @case('datetime-local')
                              <input type="{{ $coluna->tipo }}" class="form-control form-control-sm form-salvar-estado"
                                name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}"
                                value="{{ $valorSalvoCol }}" {{ $coluna->obrigatoria ? 'required' : '' }} {{
                              $bloqueadoCol ? 'disabled' : '' }}>
                              @break

                              @case('file')
                              <input type="file" class="form-control form-control-sm form-salvar-estado"
                                name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" {{ $coluna->obrigatoria
                              && !$valorSalvoCol ? 'required' : '' }} {{ $bloqueadoCol ? 'disabled' : '' }}
                              @if($coluna->accept) accept="{{ $coluna->accept }}" @endif>
                              @if($coluna->accept)
                              <div class="form-text text-muted">
                                Formatos aceitos: {{ str_replace(',', ', ', $coluna->accept) }}
                              </div>
                              @endif
                              @if($valorSalvoCol)
                              <div class="form-text text-success mt-1" style="font-size: 0.7rem;"><i
                                  class="bi bi-check-circle"></i> Arquivo já enviado.</div>
                              @endif
                              @break

                              @case('select')
                              <select class="form-select form-select-sm form-salvar-estado"
                                name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}" {{ $coluna->obrigatoria
                                ? 'required' : '' }} {{ $bloqueadoCol ? 'disabled' : '' }}>
                                <option value="" disabled {{ !$valorSalvoCol ? 'selected' : '' }}>Selecione</option>
                                @foreach($coluna->opcoes as $op)
                                <option value="{{ $op->valor }}" {{ $valorSalvoCol==$op->valor ? 'selected' : '' }}>{{
                                  $op->rotulo }}</option>
                                @endforeach
                              </select>
                              @break

                              @case('radio')
                              <div>
                                @foreach($coluna->opcoes as $op)
                                <div class="form-check mb-1">
                                  <input class="form-check-input form-salvar-estado" type="radio"
                                    name="respostas[{{ $coluna->id }}]" data-indice="{{ $indice }}"
                                    id="op_{{ $op->id }}_{{ $indice }}" value="{{ $op->valor }}" {{ $coluna->obrigatoria
                                  ? 'required' : '' }} {{ $valorSalvoCol == $op->valor ? 'checked' : '' }} {{
                                  $bloqueadoCol ? 'disabled' : '' }}>
                                  <label class="form-check-label" style="font-size: 0.85rem;"
                                    for="op_{{ $op->id }}_{{ $indice }}">{{ $op->rotulo }}</label>
                                </div>
                                @endforeach
                              </div>
                              @break

                              @case('checkbox')
                              <div>
                                @foreach($coluna->opcoes as $op)
                                <div class="form-check mb-1">
                                  <input class="form-check-input form-salvar-estado" type="checkbox"
                                    name="respostas[{{ $coluna->id }}][]" data-indice="{{ $indice }}"
                                    id="op_{{ $op->id }}_{{ $indice }}" value="{{ $op->valor }}" {{ in_array($op->valor,
                                  $arrayValoresCol) ? 'checked' : '' }} {{ $bloqueadoCol ? 'disabled' : '' }}>
                                  <label class="form-check-label" style="font-size: 0.85rem;"
                                    for="op_{{ $op->id }}_{{ $indice }}">{{ $op->rotulo }}</label>
                                </div>
                                @endforeach
                              </div>
                              @break

                              @case('location')
                              @php
                              $localDataCol = is_string($valorSalvoCol) && json_decode($valorSalvoCol, true) ?
                              json_decode($valorSalvoCol, true) : ['nome' => $valorSalvoCol, 'lat' => '', 'lng' => ''];
                              $localNomeCol = $localDataCol['nome'] ?? '';
                              $localLatCol = $localDataCol['lat'] ?? '';
                              $localLngCol = $localDataCol['lng'] ?? '';
                              @endphp
                              <div class="location-wrapper position-relative" id="location-wrapper-{{ $inputUnicoId }}">
                                @if($bloqueadoCol)
                                <div class="position-absolute w-100 h-100 bg-white bg-opacity-50"
                                  style="z-index: 10; top:0; left:0;"></div>
                                @endif
                                <div class="d-flex justify-content-between mb-1">
                                  <span class="text-muted small" style="font-size: 0.7rem;">Pesquise no mapa</span>
                                  <a class="text-decoration-none small" style="font-size: 0.7rem;"
                                    onclick="toggleLocationInput('{{ $inputUnicoId }}')"
                                    href="javascript:void(0)">Manual?</a>
                                </div>
                                <div class="gap-2 mb-2" id="div-select-local-{{ $inputUnicoId }}">
                                  <select class="form-select select-location-tom form-salvar-estado"
                                    id="select-local-{{ $inputUnicoId }}" name="respostas[{{ $coluna->id }}]"
                                    data-indice="{{ $indice }}" data-map-id="{{ $inputUnicoId }}" {{
                                    $coluna->obrigatoria ? 'required' : '' }} {{ $bloqueadoCol ? 'disabled' : '' }}>
                                    <option value="">Pesquisar...</option>
                                    @if($localNomeCol)
                                    <option value="{{ $localNomeCol }}" selected>{{ $localNomeCol }}</option>
                                    @endif
                                  </select>
                                </div>
                                <div class="d-none mb-2" id="div-chose-{{ $inputUnicoId }}">
                                  <input type="text" class="form-control form-control-sm form-salvar-estado"
                                    id="input-local-{{ $inputUnicoId }}" data-indice="{{ $indice }}"
                                    placeholder="Nome do local" value="{{ $localNomeCol }}" {{ $bloqueadoCol
                                    ? 'disabled' : '' }}>
                                </div>
                                <div id="map-{{ $inputUnicoId }}" class="map-container border rounded"
                                  style="height: 150px; width: 100%; z-index: 1;" data-saved-lat="{{ $localLatCol }}"
                                  data-saved-lng="{{ $localLngCol }}"></div>
                                <input type="hidden" class="form-salvar-estado" name="latitude[{{ $coluna->id }}]"
                                  data-indice="{{ $indice }}" id="latitude-{{ $inputUnicoId }}"
                                  value="{{ $localLatCol }}" {{ $bloqueadoCol ? 'disabled' : '' }}>
                                <input type="hidden" class="form-salvar-estado" name="longitude[{{ $coluna->id }}]"
                                  data-indice="{{ $indice }}" id="longitude-{{ $inputUnicoId }}"
                                  value="{{ $localLngCol }}" {{ $bloqueadoCol ? 'disabled' : '' }}>
                              </div>
                              @break

                              @endswitch
                            </div>
                            @endforeach
                          </div>
                        </div>
                      </div>
                      @endforeach
                    </div>

                    <button type="button" class="btn btn-primary btn-sm mt-2"
                      onclick="adicionarLinhaTabela('{{ $pergunta->id }}')">
                      <i class="ti ti-plus"></i> Adicionar Novo Item
                    </button>
                  </div>
                  @break
                  {{-- ========================================== --}}
                  {{-- FIM DA LÓGICA DE TABELA / REPEATER --}}
                  {{-- ========================================== --}}

                  @endswitch
                </div>
                @endforeach
                <form action="{{ route('report.finish', $submissao->id) }}" method="POST" id="form-finalizar">
                  <div class="mt-5">
                    <label class="form-check">
                      <input class="form-check-input" type="checkbox" name="aceite" required {{ old('aceite')
                        ? 'checked' : '' }}>
                      <span class="form-check-label">
                        Declaro que li e aceito os <a href="{{ route('terms.index') }}" target="_blank">Termos de Uso e
                          Política de Privacidade</a>. Estou ciente de que meus dados serão coletados e tratados de forma
                        segura para a prestação dos serviços e comunicações relacionadas.
                      </span>
                    </label>
                  </div>
                  <div class="d-flex justify-content-between mt-1 border-top pt-3">
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
                    <div >
                      @csrf
                      @method('DELETE')
                      <button type="button" id="btn-finalizar-fake" class="btn btn-success">
                        Finalizar e Enviar
                      </button>
                    </div>
                    @endif
                  </div>
                </form>
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
          },
          error: function(xhr) {
              // Se retornar 403 (Erro de Segurança do Backend para campos bloqueados)
              let mensagemErro = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Ocorreu um erro ao salvar esta resposta.';
              exibirToast('Atenção!', mensagemErro, 'danger');
              
              // Se tentou burlar desabilitando o campo, força ele a voltar ao disabled original recarregando ou repintando
              if(xhr.status === 403) {
                  input.prop('disabled', true);
              }
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