@extends('templates.template')

@section('styles')
@endsection

@section('content')
<div class="page-body row">
    <div class="m-0 p-0 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 text-primary fw-bold">Validação: {{ $submissao->relatorio->titulo }}</h3>
            <p class="mb-0 text-muted">Avaliando submissão: {{ $submissao->acao->titulo }}</p>
        </div>
        <a href="{{route('report.monitor', $submissao->id_relatorio)}}" class="btn">Voltar página</a>
    </div>

    <form action="{{route('report.validate.store', $submissao->id)}}" method="POST" id="form-validacao" class="p-0">
        @csrf

        <ul class="nav nav-tabs mb-4" id="secoesTabs" role="tablist">
            @foreach($secoes as $secao)
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold {{ $loop->first ? 'active' : '' }}" id="tab-{{ $secao['id'] }}"
                    data-bs-toggle="tab" data-bs-target="#pane-{{ $secao['id'] }}" type="button" role="tab"
                    aria-controls="pane-{{ $secao['id'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{
                    $secao['titulo'] }}
                </button>
            </li>
            @endforeach
        </ul>

        <div class="tab-content" id="secoesTabsContent">
            @foreach($secoes as $secao)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $secao['id'] }}"
                role="tabpanel" aria-labelledby="tab-{{ $secao['id'] }}" tabindex="0">

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white pb-2 d-flex flex-column align-items-start text-start">
                        <h4 class="mb-1 fw-bold text-dark">{{ $secao['titulo'] }}</h4>
                        <p class="mb-0 text-muted small">{{ $secao['descricao'] }}</p>
                    </div>

                    <div class="card-body p-4">
                        @foreach($secao['perguntas'] as $pergunta)
                        <div class="pergunta-validacao mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="fw-bold mb-2 text-dark fs-5">{{ $pergunta['enunciado'] }}</label>

                                    @if($pergunta['tipo'] === 'tabela')
                                    @if(empty($pergunta['valores_tabela']))
                                    <p class="text-muted fst-italic">Nenhum item adicionado.</p>
                                    @else
                                    <div class="table-responsive mt-2">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    @foreach($pergunta['valores_tabela'][0] as $coluna)
                                                    <th>{{ $coluna['enunciado'] }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pergunta['valores_tabela'] as $linha)
                                                <tr>
                                                    @foreach($linha as $coluna)
                                                    <td>{!! $coluna['valor'] !!}</td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                    @else
                                    <div class="p-2 bg-light rounded border">
                                        <span class="fs-6">{!! $pergunta['valor'] !!}</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="col-12 py-0">
                                    <div class="d-flex gap-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input radio-status" type="radio"
                                                name="validacao[{{ $pergunta['id'] }}][status]"
                                                id="aprovar_{{ $pergunta['id'] }}" value="aprovado" required>
                                            <label class="form-check-label text-success fw-semibold"
                                                for="aprovar_{{ $pergunta['id'] }}">
                                                Validar
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input radio-status" type="radio"
                                                name="validacao[{{ $pergunta['id'] }}][status]"
                                                id="reprovar_{{ $pergunta['id'] }}" value="correcao">
                                            <label class="form-check-label text-danger fw-semibold"
                                                for="reprovar_{{ $pergunta['id'] }}">
                                                Solicitar Correção
                                            </label>
                                        </div>
                                    </div>

                                    <div class="feedback-box mt-2" style="display: none;">
                                        <textarea class="form-control w-100"
                                            name="validacao[{{ $pergunta['id'] }}][feedback]" rows="3"
                                            placeholder="Descreva o motivo ou o que precisa ser corrigido..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        <div class="card shadow-sm mt-2">
            <div class="card-body d-flex justify-content-between align-items-center bg-light">
                <span class="text-muted"><i class="ti ti-info-circle"></i> Avalie todos os itens e preencha os feedbacks necessários para liberar o envio.</span>
                <button type="submit" id="btn-concluir" class="btn btn-success" disabled>
                    <i class="ti ti-device-floppy icon"></i> Concluir Avaliação
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-validacao');
    const btnConcluir = document.getElementById('btn-concluir');
    const perguntas = document.querySelectorAll('.pergunta-validacao');

    // Função que verifica se o formulário inteiro está pronto para envio
    function checarFormularioValido() {
        let formularioValido = true;

        perguntas.forEach(pergunta => {
            const radioChecado = pergunta.querySelector('.radio-status:checked');
            const textarea = pergunta.querySelector('textarea');

            if (!radioChecado) {
                // Se nem aprovado nem reprovado foi marcado
                formularioValido = false; 
            } else if (radioChecado.value === 'correcao') {
                // Se marcou correção, o textarea não pode estar vazio (removemos espaços em branco com trim())
                if (textarea.value.trim() === '') {
                    formularioValido = false;
                }
            }
        });

        // Ativa ou desativa o botão baseado na validação
        btnConcluir.disabled = !formularioValido;
    }

    // Configura os eventos para cada bloco de pergunta
    perguntas.forEach(pergunta => {
        const radios = pergunta.querySelectorAll('.radio-status');
        const feedbackBox = pergunta.querySelector('.feedback-box');
        const textarea = pergunta.querySelector('textarea');
        
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'correcao') {
                    feedbackBox.style.display = 'block';
                    textarea.setAttribute('required', 'required');
                } else {
                    feedbackBox.style.display = 'none';
                    textarea.removeAttribute('required');
                    textarea.value = '';
                }
                
                checarFormularioValido();
            });
        });

        textarea.addEventListener('input', function() {
            checarFormularioValido();
        });
    });

    checarFormularioValido();

    form.addEventListener('invalid', function(e) {
        let pane = e.target.closest('.tab-pane');
        if (pane) {
            let tabId = pane.getAttribute('aria-labelledby');
            let tab = document.getElementById(tabId);
            if (tab) {
                let tabInstance = new bootstrap.Tab(tab);
                tabInstance.show();
            }
        }
    }, true);
});
</script>
@endsection