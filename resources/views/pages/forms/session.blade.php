@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
  <div class="m-0 p-0 mb-4 row">
    <div class="btn-list col-12 col-md-6 p-0 m-0">
      <bottom class="btn" data-bs-toggle="collapse" data-bs-target="#editar" aria-expanded="false"
        aria-controls="collapseExample">Editar seção</bottom>
      <a href="" class="btn">Excluir seção</a>
      <a href="{{route('forms.index')}}" class="btn">Voltar página</a>
    </div>
  </div>
  <div class="collapse m-0 p-0 mb-3" id="editar">
    <div class="card card-body m-0 p-3">
      <form action="{{ route('sessions.update', $session->id) }}" method="POST" class="row">
        @csrf
        @include('components.form-elements.input.input', [
        'title' => 'Título',
        'type' => 'text',
        'class' => 'mb-3 col-12 col-md-4',
        'name' => 'titulo',
        'required' => 'true',
        'placeholder' => 'Insira um título para a seção',
        'value' => $session->titulo
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Ordem da Seção',
        'type' => 'number',
        'class' => 'mb-3 col-12 col-md-2',
        'name' => 'ordem',
        'min' => 1,
        'required' => 'true',
        'placeholder' => 'Insira a ordem da seção',
        'value' => $session->ordem
        ])

        @include('components.form-elements.input.input', [
        'title' => 'Descrição',
        'type' => 'text',
        'class' => 'mb-3 col-12 col-md-4',
        'name' => 'descricao',
        'required' => 'true',
        'placeholder' => 'Insira uma descrição para a seção',
        'value' => $session->descricao
        ])


        <div class="col-12 col-md-2">
          <label class="form-label d-block">&nbsp;</label>
          <div class="d-flex">
            <button class="btn btn-green w-100" type="submit">Salvar</button>
          </div>
        </div>

      </form>
    </div>
  </div>
  <div class="row m-0 p-0 gap-1">
    <div class="col-12 col-md m-0 p-0">
      <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
          {{-- Você pode colocar um título da sessão aqui se quiser --}}
          {{-- <h4 class="mb-4">{{ $session->titulo }}</h4> --}}

          @foreach ($session->perguntas as $pergunta)
          <div class="pergunta-item mb-4">
            {{-- Enunciado da Pergunta --}}
            <label class="form-label fw-bold fs-6 mb-2">
              {{ $pergunta->enunciado }}
              @if($pergunta->obrigatoria)
              <span class="text-danger" title="Obrigatório">*</span>
              @endif
            </label>

            {{-- Renderiza o input de acordo com o tipo --}}
            @switch($pergunta->tipo)

            @case('text')
            <input type="text" class="form-control" name="respostas[{{ $pergunta->id }}]" {{ $pergunta->obrigatoria ?
            'required' : '' }}
            @if($pergunta->min) minlength="{{ $pergunta->min }}" @endif
            @if($pergunta->max) maxlength="{{ $pergunta->max }}" @endif
            @if($pergunta->regex) pattern="{{ $pergunta->regex }}" @endif
            placeholder="Sua resposta aqui">

            {{-- Infozinho para Text --}}
            @if($pergunta->min || $pergunta->max || $pergunta->regex)
            <div class="form-text text-muted">
              @if($pergunta->min) Mín: {{ $pergunta->min }} caracteres. @endif
              @if($pergunta->max) Máx: {{ $pergunta->max }} caracteres. @endif
              @if($pergunta->regex) <span title="{{ $pergunta->regex }}">Requer formato específico.</span> @endif
            </div>
            @endif
            @break

            @case('textarea')
            <textarea class="form-control" name="respostas[{{ $pergunta->id }}]" rows="3" {{ $pergunta->obrigatoria ? 'required' : '' }}
                                      @if($pergunta->min) minlength="{{ $pergunta->min }}" @endif
                                      @if($pergunta->max) maxlength="{{ $pergunta->max }}" @endif
                                      placeholder="Digite sua resposta..."></textarea>

            {{-- Infozinho para Textarea --}}
            @if($pergunta->min || $pergunta->max)
            <div class="form-text text-muted">
              @if($pergunta->min) Mín: {{ $pergunta->min }} caracteres. @endif
              @if($pergunta->max) Máx: {{ $pergunta->max }} caracteres. @endif
            </div>
            @endif
            @break

            @case('number')
            <input type="number" class="form-control" name="respostas[{{ $pergunta->id }}]" {{ $pergunta->obrigatoria ?
            'required' : '' }}
            @if($pergunta->min) min="{{ $pergunta->min }}" @endif
            @if($pergunta->max) max="{{ $pergunta->max }}" @endif
            @if($pergunta->step) step="{{ $pergunta->step }}" @endif
            placeholder="Apenas números">

            {{-- Infozinho para Number --}}
            @if($pergunta->min || $pergunta->max || $pergunta->step)
            <div class="form-text text-muted">
              @if($pergunta->min) Valor mín: {{ $pergunta->min }}. @endif
              @if($pergunta->max) Valor máx: {{ $pergunta->max }}. @endif
              @if($pergunta->step) Intervalo numérico: {{ $pergunta->step }}. @endif
            </div>
            @endif
            @break

            @case('file')
            <input type="file" class="form-control" name="respostas[{{ $pergunta->id }}]" {{ $pergunta->obrigatoria ?
            'required' : '' }}
            @if($pergunta->accept) accept="{{ $pergunta->accept }}" @endif>

            {{-- Infozinho para File --}}
            @if($pergunta->accept)
            <div class="form-text text-muted">
              Formatos aceitos: {{ str_replace(',', ', ', $pergunta->accept) }}
            </div>
            @endif
            @break

            @case('select')
            <select class="form-select" name="respostas[{{ $pergunta->id }}]" {{ $pergunta->obrigatoria ? 'required' :
              '' }}>
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
                  id="opcao_{{ $opcao->id }}" value="{{ $opcao->id }}" {{ $pergunta->obrigatoria ? 'required' : '' }}>
                <label class="form-check-label" for="opcao_{{ $opcao->id }}">
                  {{ $opcao->rotulo }}
                </label>
              </div>
              @endforeach
            </div>
            @break

            @case('checkbox')
            <div>
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

            @default
            <p class="text-muted">Tipo de pergunta não suportado.</p>

            @endswitch
          </div>

          {{-- Adiciona uma linha divisória entre as perguntas, mas ignora na última --}}
          @if(!$loop->last)
          <hr class="text-muted opacity-25 my-4">
          @endif

          @endforeach
        </div>
      </div>
    </div>
    <div class="col-12 col-md m-0 p-0">
      <div class="card">
        <form method="post" action="{{route('sessions.storeQuestion', $session->id)}}" class="card-body">
          @csrf
          <p class="fw-bold">Adicionar nova pergunta</p>

          <x-form-elements.select.select title="Tipo de pergunta" id="tipo" name="tipo" class="col-12" required="true">
            <x-slot:options>
              <option value="" disabled selected>Selecione</option>
              <option value="text">Texto normal</option>
              <option value="textarea">Texto grande</option>
              <option value="select">Seletor</option>
              <option value="checkbox">Caixas de verificação (Checkbox)</option>
              <option value="radio">Botão de opção (Radio)</option>
              <option value="file">Arquivo</option>
              <option value="number">Número</option>
            </x-slot:options>
          </x-form-elements.select.select>

          @include('components.form-elements.input.input', [
          'title' => 'Enunciado',
          'type' => 'text',
          'class' => 'mb-3 col-12',
          'name' => 'enunciado',
          'required' => 'true',
          'placeholder' => 'Enunciado da pergunta'
          ])

          <x-form-elements.select.select title="A pergunta é obrigatória?" id="obrigatoria" name="obrigatoria"
            class="col-12 mb-3" required="true">
            <x-slot:options>
              <option value="" disabled selected>Selecione</option>
              <option value="0">Não</option>
              <option value="1">Sim</option>
            </x-slot:options>
          </x-form-elements.select.select>

          <div id="area-validacoes" class="row mb-3 d-none">
            <p class="fw-bold mb-2">Configurações de Validação (Opcional)</p>

            <div id="container-min-max" class="col-12 d-none">
              <div class="row">
                <div class="col-6">
                  <label id="label-min" class="form-label">Mínimo</label>
                  <input type="number" name="min" class="form-control" placeholder="Ex: 0">
                </div>
                <div class="col-6">
                  <label id="label-max" class="form-label">Máximo</label>
                  <input type="number" name="max" class="form-control" placeholder="Ex: 100">
                </div>
              </div>
            </div>

            <div id="container-step" class="col-12 d-none">
              <label class="form-label">Intervalo (Step)</label>
              <input type="number" name="step" step="any" class="form-control" placeholder="Ex: 0.1">
            </div>

            <div id="container-accept" class="col-12 d-none mb-3">
              <label class="form-label fw-bold">Tipos de Arquivos Aceitos</label>
              <div class="row">
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept[]" value="image/*" id="accept-image">
                    <label class="form-check-label" for="accept-image">Imagens (PNG, JPG, etc)</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept[]" value=".pdf" id="accept-pdf">
                    <label class="form-check-label" for="accept-pdf">Documentos PDF</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept[]" value=".doc,.docx" id="accept-word">
                    <label class="form-check-label" for="accept-word">Microsoft Word</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept[]" value=".zip,.rar,.7z"
                      id="accept-zip">
                    <label class="form-check-label" for="accept-zip">Arquivos Compactados</label>
                  </div>
                </div>

                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept[]" value=".xls,.xlsx"
                      id="accept-excel">
                    <label class="form-check-label" for="accept-excel">Microsoft Excel</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept[]" value="video/*" id="accept-video">
                    <label class="form-check-label" for="accept-video">Vídeos (MP4, AVI, etc)</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept[]" value="audio/*" id="accept-audio">
                    <label class="form-check-label" for="accept-audio">Áudios (MP3, WAV, etc)</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept[]" value=".csv" id="accept-csv">
                    <label class="form-check-label" for="accept-csv">Arquivos CSV</label>
                  </div>
                </div>
              </div>
              <small class="text-muted mt-1 d-block">Se nenhuma opção for marcada, todos os tipos de arquivos serão
                aceitos.</small>
            </div>

            <div id="container-regex" class="col-12 d-none mt-2">
              <label class="form-label">Regex (Máscara/Padrão)</label>
              <input type="text" name="regex" class="form-control" placeholder="Ex: ^[0-9]{3}$">
            </div>
          </div>

          <div id="area-opcoes" class="mb-3 d-none">
            <p class="fw-bold mb-2">Opções de Resposta</p>
            <div id="lista-opcoes">
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="adicionarOpcao()">
              + Adicionar Opção
            </button>
          </div>

          <div class="d-flex justify-content-end">
            <button class="btn btn-success" type="submit">Salvar</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
        const selectTipo = document.getElementById('tipo');
        
        // Contêineres principais
        const areaValidacoes = document.getElementById('area-validacoes');
        const areaOpcoes = document.getElementById('area-opcoes');
        
        // Contêineres específicos de validação
        const containerMinMax = document.getElementById('container-min-max');
        const containerStep = document.getElementById('container-step');
        const containerAccept = document.getElementById('container-accept');
        const containerRegex = document.getElementById('container-regex');
        
        // Labels dinâmicos
        const labelMin = document.getElementById('label-min');
        const labelMax = document.getElementById('label-max');

        selectTipo.addEventListener('change', function () {
            const tipo = this.value;

            // ==========================================
            // 1. ZERAR TODOS OS DADOS (Prevenção de dados fantasmas)
            // ==========================================
            
            // Limpar inputs de texto/número
            document.querySelector('input[name="min"]').value = '';
            document.querySelector('input[name="max"]').value = '';
            document.querySelector('input[name="step"]').value = '';
            document.querySelector('input[name="regex"]').value = '';

            // Desmarcar todos os checkboxes de "Accept"
            const checkboxesAccept = document.querySelectorAll('input[name="accept[]"]');
            checkboxesAccept.forEach(checkbox => checkbox.checked = false);

            // Limpar a lista de opções dinâmicas (esvazia a div)
            const listaOpcoes = document.getElementById('lista-opcoes');
            listaOpcoes.innerHTML = ''; 


            // ==========================================
            // 2. ESCONDER TUDO (Reset visual)
            // ==========================================
            areaValidacoes.classList.add('d-none');
            areaOpcoes.classList.add('d-none');
            containerMinMax.classList.add('d-none');
            containerStep.classList.add('d-none');
            containerAccept.classList.add('d-none');
            containerRegex.classList.add('d-none');


            // ==========================================
            // 3. MOSTRAR APENAS O NECESSÁRIO
            // ==========================================
            if (['text', 'textarea'].includes(tipo)) {
                areaValidacoes.classList.remove('d-none');
                containerMinMax.classList.remove('d-none');
                containerRegex.classList.remove('d-none');
                
                labelMin.innerText = "Mín. Caracteres";
                labelMax.innerText = "Máx. Caracteres";

            } else if (tipo === 'number') {
                areaValidacoes.classList.remove('d-none');
                containerMinMax.classList.remove('d-none');
                containerStep.classList.remove('d-none');
                
                labelMin.innerText = "Valor Mínimo";
                labelMax.innerText = "Valor Máximo";

            } else if (tipo === 'file') {
                areaValidacoes.classList.remove('d-none');
                containerAccept.classList.remove('d-none');

            } else if (['select', 'checkbox', 'radio'].includes(tipo)) {
                areaOpcoes.classList.remove('d-none');
                
                // Como limpamos a lista ali em cima, sempre vai estar vazia ao selecionar isso,
                // então já adicionamos a primeira opção automaticamente pro usuário não ter que clicar no botão.
                adicionarOpcao();
            }
        });
    });

    // Função para adicionar uma nova opção de select/radio/checkbox
    function adicionarOpcao() {
        const lista = document.getElementById('lista-opcoes');
        const index = lista.children.length; 

        const divRow = document.createElement('div');
        divRow.className = 'input-group mb-2';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'opcoes[]'; 
        input.className = 'form-control';
        input.placeholder = `Opção ${index + 1}`;
        input.required = true;

        const btnRemove = document.createElement('button');
        btnRemove.type = 'button';
        btnRemove.className = 'btn btn-outline-danger';
        btnRemove.innerHTML = 'Remover';
        btnRemove.onclick = function () {
            divRow.remove();
        };

        divRow.appendChild(input);
        divRow.appendChild(btnRemove);
        lista.appendChild(divRow);
    }
</script>
@endsection