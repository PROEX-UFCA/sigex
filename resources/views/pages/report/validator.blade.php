@extends('templates.template')

@section('styles')
<style>
    .table-validation td {
        vertical-align: top;
    }

    .validation-block {
        background-color: #f8f9fa;
        border-radius: 6px;
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #e9ecef;
    }
</style>
@endsection

@section('content')
<div class="page-body row">
    <div class="m-0 p-0 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 text-primary fw-bold">Validação: {{ $submissao->relatorio->titulo }}</h3>
            <p class="mb-0 text-muted">Avaliando submissão: {{$submissao->id_acao != null ? $submissao->acao->titulo : $submissao->user->name}}</p>
        </div>
        {{-- <a href="{{route('report.monitor', $submissao->id_relatorio)}}" class="btn">Voltar página</a> --}}
        <a href="{{ old('previous', url()->previous()) }}" class="btn">Voltar página</a>
    </div>

    <form action="{{route('report.validate.store', $submissao->id)}}" method="POST" id="form-validacao" class="p-0">
        @csrf
        <input type="hidden" name="previous" value="{{ old('previous', url()->previous()) }}">

        <ul class="nav nav-tabs mb-4" id="secoesTabs" role="tablist">
            @foreach($secoes as $secao)
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold d-flex align-items-center gap-2 {{ $loop->first ? 'active' : '' }}"
                    id="tab-{{ $secao['id_secao'] }}" data-bs-toggle="tab"
                    data-bs-target="#pane-{{ $secao['id_secao'] }}" type="button" role="tab"
                    aria-controls="pane-{{ $secao['id_secao'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    <span>{{ $secao['titulo'] }}</span>
                    <i class="ti ti-circle-check fs-5 status-icon text-success d-none"></i>
                </button>
            </li>
            @endforeach
        </ul>

        <div class="tab-content" id="secoesTabsContent">
            @foreach($secoes as $secao)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $secao['id_secao'] }}"
                role="tabpanel" aria-labelledby="tab-{{ $secao['id_secao'] }}" tabindex="0">

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white pb-2 d-flex flex-column align-items-start text-start">
                        <h4 class="mb-1 fw-bold text-dark">{{ $secao['titulo'] }}</h4>
                        <p class="mb-0 text-muted small">{{ $secao['descricao'] }}</p>
                    </div>

                    <div class="card-body p-4">
                        @foreach($secao['perguntas'] as $pergunta)
                        <div class="pergunta-container mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">

                            <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                <label class="fw-bold text-dark fs-5 mb-0">{{ $pergunta['enunciado'] }}</label>

                                @if($pergunta['obrigatorio'] ?? true)
                                <span
                                    class="badge fs-6 bg-danger-subtle text-danger border border-danger-subtle">Obrigatório</span>
                                @else
                                <span
                                    class="badge fs-6 bg-secondary-subtle text-secondary border border-secondary-subtle">Opcional</span>
                                @endif

                                @if(!empty($pergunta['regras']))
                                <span class="badge fs-6 bg-info-subtle text-info border border-info-subtle"
                                    data-bs-toggle="tooltip" title="{{ $pergunta['regras'] }}">
                                    <i class="ti ti-info-circle me-1"></i> {{ $pergunta['regras'] }}
                                </span>
                                @endif

                                @if(!empty($pergunta['opcoes']))
                                <span class="badge fs-6 bg-purple-lt text-purple border border-purple-subtle"
                                    data-bs-toggle="tooltip" title="{{ $pergunta['opcoes'] }}">
                                    <i class="ti ti-list me-1"></i>{{ $pergunta['opcoes'] }}
                                </span>
                                @endif
                            </div>

                            @if($pergunta['tipo'] === 'tabela')
                            @if(empty($pergunta['valores_tabela']))
                            <p class="text-muted fst-italic">Nenhum item adicionado.</p>
                            @else
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered table-validation">
                                    <thead class="table-light">
                                        <tr>
                                            @foreach($pergunta['valores_tabela'][0] as $coluna)
                                            <th class="fw-semibold">
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    {{ $coluna['enunciado'] }}
                                                    @if($coluna['obrigatorio'] ?? true)
                                                    <span
                                                        class="badge fs-6 bg-danger-subtle text-danger border border-danger-subtle">Obrigatório</span>
                                                    @else
                                                    <span
                                                        class="badge fs-6 bg-secondary-subtle text-secondary border border-secondary-subtle">Opcional</span>
                                                    @endif

                                                    @if(!empty($coluna['regras']))
                                                    <span
                                                        class="badge bg-info-subtle text-info border border-info-subtle"
                                                        data-bs-toggle="tooltip" title="{{ $coluna['regras'] }}">
                                                        <i class="ti ti-info-circle me-1"></i> {{ $coluna['regras'] }}
                                                    </span>
                                                    @endif

                                                    @if(!empty($coluna['opcoes']))
                                                    <span
                                                        class="badge fs-6 bg-purple-lt text-purple border border-purple-subtle"
                                                        data-bs-toggle="tooltip" title="{{ $coluna['opcoes'] }}">
                                                        <i class="ti ti-list me-1"></i>{{ $coluna['opcoes'] }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pergunta['valores_tabela'] as $linha)
                                        <tr>
                                            @foreach($linha as $coluna)
                                            @php $idResp = $coluna['id_resposta']; @endphp
                                            <td>
                                                <div class="mb-2">
                                                    {!! $coluna['valor'] ?? '<span class="text-muted fst-italic">Não
                                                        respondido</span>' !!}
                                                </div>
                                                <div class="validation-block">
                                                    @include('partials.validation-inputs', ['id' => $idResp, 'data' =>
                                                    $coluna])
                                                </div>
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            @else
                            <div class="p-3 bg-light rounded border mb-3">
                                <span class="fs-6">{!! $pergunta['valor'] ?? '<span class="text-muted fst-italic">Não
                                        respondido</span>' !!}</span>
                            </div>

                            @php $idResp = $pergunta['id_resposta']; @endphp
                            <div class="validation-block">
                                @include('partials.validation-inputs', ['id' => $idResp, 'data' => $pergunta])
                            </div>
                            @endif

                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        <div class="card shadow-sm mt-2 sticky-bottom">
            <div class="card-body d-flex justify-content-between align-items-center bg-light border-top">
                <span class="text-muted"><i class="ti ti-info-circle"></i> Avalie todos os itens (e células de tabelas)
                    para liberar a conclusão.</span>
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
    const validationBlocks = document.querySelectorAll('.validation-block');
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    function checarFormularioValido() {
        let formularioValido = true;

        validationBlocks.forEach(block => {
            const radioChecado = block.querySelector('.radio-status:checked');
            const textarea = block.querySelector('textarea');

            if (!radioChecado) {
                formularioValido = false; 
            } else if (radioChecado.value == 0) {
                if (textarea.value.trim() === '') {
                    formularioValido = false;
                }
            }
        });

        btnConcluir.disabled = !formularioValido;
        atualizarStatusSecoes();
    }

    function atualizarStatusSecoes() {
        document.querySelectorAll('.tab-pane').forEach(pane => {
            const tabId = pane.getAttribute('aria-labelledby');
            const tabBtn = document.getElementById(tabId);
            const statusIcon = tabBtn.querySelector('.status-icon');
            const blocks = pane.querySelectorAll('.validation-block');
            
            let todasValidadas = true;
            let temBlocos = blocks.length > 0;

            blocks.forEach(block => {
                const checked = block.querySelector('.radio-status:checked');
                const textarea = block.querySelector('textarea');
                
                if (!checked) todasValidadas = false;
                else if (checked.value == 0 && textarea.value.trim() === '') todasValidadas = false;
            });

            if (temBlocos && todasValidadas) {
                statusIcon.classList.remove('d-none');
            } else {
                statusIcon.classList.add('d-none');
            }
        });
    }

    validationBlocks.forEach(block => {
        const radios = block.querySelectorAll('.radio-status');
        const feedbackBox = block.querySelector('.feedback-box');
        const textarea = block.querySelector('textarea');
        
        // Verifica o estado inicial (útil para o old() do Laravel)
        const radioChecado = block.querySelector('.radio-status:checked');
        if(radioChecado && radioChecado.value == 0) {
            feedbackBox.style.display = 'block';
            textarea.setAttribute('required', 'required');
        }

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value == 0) {
                    feedbackBox.style.display = 'block';
                    textarea.setAttribute('required', 'required');
                } else {
                    feedbackBox.style.display = 'none';
                    textarea.removeAttribute('required');
                    textarea.value = ''; // Limpa o textarea ao aprovar
                }
                checarFormularioValido();
            });
        });

        textarea.addEventListener('input', function() {
            checarFormularioValido();
        });
    });

    checarFormularioValido();

    // Redireciona o usuário para a aba que contém o erro ao tentar enviar o form
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