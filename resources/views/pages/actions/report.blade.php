@extends('templates.template')

@section('styles')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link href="{{ asset('assets/libs/tom-select/dist/css/tom-select.bootstrap5.css') }}" rel="stylesheet" />
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
          <form action="" method="POST" id="formWizard">
            @csrf
            <ul class="nav nav-pills nav-justified mb-4" id="wizardTabs" role="tablist">
              @foreach ($submissao->relatorio->formulario->secoes as $index => $secao)
              <li class="nav-item" role="presentation">
                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-btn-{{ $secao->id }}"
                  data-bs-toggle="pill" data-bs-target="#secao-{{ $secao->id }}" type="button" role="tab" {{ $index> 0 ?
                  'disabled' : '' }}>
                  Seção {{ $index + 1 }}.
                </button>
              </li>
              @endforeach
            </ul>

            <div class="tab-content card shadow-sm p-4" id="wizardContent">
              @foreach ($submissao->relatorio->formulario->secoes as $index => $secao)
              <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="secao-{{ $secao->id }}"
                role="tabpanel">

                <h4 class="mb-1 text-primary">{{ $secao->titulo }}</h4>
                <p>{{ $secao->descricao }}</p>

                @foreach ($secao->perguntas as $pergunta)
                <div class="mb-4">
                  <label class="form-label fw-bold">
                    {{ $pergunta->enunciado }}
                    @if($pergunta->obrigatoria)
                    <span class="text-danger" title="Campo obrigatório">*</span>
                    @endif
                  </label>

                  @switch($pergunta->tipo)

                  {{-- Texto e Localização compartilham as mesmas regras de validação de string --}}
                  @case('text')
                  <input type="text" class="form-control form-salvar-estado" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria ? 'required' : '' }}
                  @if($pergunta->min) minlength="{{ $pergunta->min }}" @endif
                  @if($pergunta->max) maxlength="{{ $pergunta->max }}" @endif
                  @if($pergunta->regex) pattern="{{ $pergunta->regex }}" @endif
                  placeholder="Sua resposta aqui">

                  @if($pergunta->min || $pergunta->max || $pergunta->regex)
                  <div class="form-text text-muted">
                    @if($pergunta->min) Mín: {{ $pergunta->min }} caracteres. @endif
                    @if($pergunta->max) Máx: {{ $pergunta->max }} caracteres. @endif
                    @if($pergunta->regex) <span title="{{ $pergunta->regex }}">Requer formato específico.</span> @endif
                  </div>
                  @endif
                  @break

                  @case('location')
                  <div class="location-wrapper" id="location-wrapper-{{ $pergunta->id }}">
                    <div class="d-flex justify-content-between mb-2">
                      <span class="text-muted small">Pesquise pelo nome, CEP ou clique no mapa</span>
                      <a class="text-decoration-none small" onclick="toggleLocationInput('{{ $pergunta->id }}')"
                        href="javascript:void(0)">
                        Não encontrou seu local?
                      </a>
                    </div>

                    {{-- Select integrado com TomSelect / Nominatim --}}
                    <div class="gap-2 mb-2" id="div-select-local-{{ $pergunta->id }}">
                      <select class="form-select select-location-tom form-salvar-estado"
                        id="select-local-{{ $pergunta->id }}" name="respostas[{{ $pergunta->id }}]"
                        data-map-id="{{ $pergunta->id }}" {{ $pergunta->obrigatoria ? 'required' : '' }}>
                        <option value="" selected>Pesquisar no mapa...</option>
                      </select>
                    </div>

                    {{-- Input manual (escondido por padrão) --}}
                    <div class="d-none mb-2" id="div-chose-{{ $pergunta->id }}">
                      <input type="text" class="form-control form-salvar-estado" id="input-local-{{ $pergunta->id }}"
                        placeholder="Digite o nome do local manualmente">
                      <div class="form-text text-danger">Modo de digitação manual ativo. O mapa será ignorado.</div>
                    </div>

                    {{-- Container do Mapa --}}
                    <div id="map-{{ $pergunta->id }}" class="map-container border rounded"
                      style="height: 300px; width: 100%; z-index: 1;"></div>

                    {{-- Inputs ocultos caso queira salvar lat/lng no banco futuramente --}}
                    <input type="hidden" name="latitude[{{ $pergunta->id }}]" id="latitude-{{ $pergunta->id }}">
                    <input type="hidden" name="longitude[{{ $pergunta->id }}]" id="longitude-{{ $pergunta->id }}">
                  </div>
                  @break

                  @case('textarea')
                  <textarea class="form-control form-salvar-estado" name="respostas[{{ $pergunta->id }}]" rows="3" {{
                    $pergunta->obrigatoria ? 'required' : '' }} 
                      @if($pergunta->min) minlength="{{ $pergunta->min }}" @endif 
                      @if($pergunta->max) maxlength="{{ $pergunta->max }}" @endif 
                      placeholder="Digite sua resposta..."></textarea>

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
                  placeholder="Apenas números">

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
                    $pergunta->obrigatoria ? 'required' : '' }}
                  @if($pergunta->accept) accept="{{ $pergunta->accept }}" @endif>

                  @if($pergunta->accept)
                  <div class="form-text text-muted">
                    Formatos aceitos: {{ str_replace(',', ', ', $pergunta->accept) }}
                  </div>
                  @endif
                  @break

                  @case('select')
                  <select class="form-select" name="respostas[{{ $pergunta->id }}]" {{ $pergunta->obrigatoria ?
                    'required' : '' }}>
                    <option value="" disabled selected>Selecione uma opção</option>
                    @foreach($pergunta->opcoes as $opcao)
                    <option value="{{ $opcao->id }}">{{ $opcao->rotulo }}</option>
                    @endforeach
                  </select>
                  @break

                  @case('radio')
                  <div>
                    @foreach($pergunta->opcoes as $opcao)
                    <div class="form-check mb-1">
                      <input class="form-check-input" type="radio" name="respostas[{{ $pergunta->id }}]"
                        id="opcao_{{ $opcao->id }}" value="{{ $opcao->id }}" {{ $pergunta->obrigatoria ? 'required' : ''
                      }}>
                      <label class="form-check-label" for="opcao_{{ $opcao->id }}">
                        {{ $opcao->rotulo }}
                      </label>
                    </div>
                    @endforeach
                  </div>
                  @break

                  @case('checkbox')
                  {{-- Adicionado atributo de dados para facilitar validações via JS caso necessário --}}
                  <div class="checkbox-group" @if($pergunta->obrigatoria) data-required="true" @endif>
                    @foreach($pergunta->opcoes as $opcao)
                    <div class="form-check mb-1">
                      <input class="form-check-input" type="checkbox" name="respostas[{{ $pergunta->id }}][]"
                        id="opcao_{{ $opcao->id }}" value="{{ $opcao->id }}">
                      <label class="form-check-label" for="opcao_{{ $opcao->id }}">
                        {{ $opcao->rotulo }}
                      </label>
                    </div>
                    @endforeach
                  </div>
                  @break

                  {{-- Datas também aceitam limites mínimos e máximos no HTML5 --}}
                  @case('date')
                  @case('datetime-local')
                  <input type="{{ $pergunta->tipo }}" class="form-control" name="respostas[{{ $pergunta->id }}]" {{
                    $pergunta->obrigatoria ? 'required' : '' }}
                  @if($pergunta->min) min="{{ $pergunta->min }}" @endif
                  @if($pergunta->max) max="{{ $pergunta->max }}" @endif>

                  @if($pergunta->min || $pergunta->max)
                  <div class="form-text text-muted">
                    @if($pergunta->min) Permitido a partir de: {{ $pergunta->min }}. @endif
                    @if($pergunta->max) Permitido até: {{ $pergunta->max }}. @endif
                  </div>
                  @endif
                  @break

                  @endswitch
                </div>
                @endforeach

                {{-- BOTÕES DE NAVEGAÇÃO DA SEÇÃO --}}
                <div class="d-flex justify-content-between mt-5 border-top pt-3">
                  @if ($index > 0)
                  <button type="button" class="btn btn-outline-secondary btn-anterior">
                    <i class="bi bi-arrow-left"></i> Anterior
                  </button>
                  @else
                  <div></div> {{-- Espaçador para manter o botão 'Próximo' na direita --}}
                  @endif

                  @if (!$loop->last)
                  <button type="button" class="btn btn-primary btn-proximo">
                    Próximo Passo <i class="bi bi-arrow-right"></i>
                  </button>
                  @else
                  <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Finalizar e Enviar
                  </button>
                  @endif
                </div>
              </div>
              @endforeach
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/tom-select/dist/js/tom-select.base.min.js') }}" defer></script>
<script src="{{ asset('assets/js/report.js') }}"></script>
@endsection