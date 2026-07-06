@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
    <div class="m-0 p-0 mb-4">
        <div class="btn-list col-12 col-md-6 p-0 m-0">
            <a href="{{route('actions.my')}}" class="btn">Voltar página</a>
        </div>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h2 class="mb-4 text-center">{{ $submissao->relatorio->formulario->titulo ?? 'Respondendo
                        Formulário' }}</h2>

                    <form action="" method="POST" id="formWizard">
                        @csrf

                        {{-- NAVEGAÇÃO SUPERIOR (Progresso) --}}
                        <ul class="nav nav-pills nav-justified mb-4" id="wizardTabs" role="tablist">
                            @foreach ($submissao->relatorio->formulario->secoes as $index => $secao)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                    id="tab-btn-{{ $secao->id }}" data-bs-toggle="pill"
                                    data-bs-target="#secao-{{ $secao->id }}" type="button" role="tab" {{-- Desabilita o
                                    clique direto nas abas futuras para forçar o uso do botão "Próximo" --}} {{ $index>
                                    0 ? 'disabled' : '' }}>
                                    {{ $index + 1 }}. {{ $secao->nome_ou_titulo }}
                                </button>
                            </li>
                            @endforeach
                        </ul>

                        {{-- CONTEÚDO DAS SEÇÕES --}}
                        <div class="tab-content card shadow-sm p-4" id="wizardContent">
                            @foreach ($submissao->relatorio->formulario->secoes as $index => $secao)
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                id="secao-{{ $secao->id }}" role="tabpanel">

                                <h4 class="mb-4 text-primary">{{ $secao->nome_ou_titulo }}</h4>

                                {{-- Renderização das Perguntas (A mesma lógica que mostrei antes) --}}
                                @foreach ($secao->perguntas as $pergunta)
                                <div class="mb-4">
                                    <label class="form-label fw-bold">{{ $pergunta->enunciado_ou_titulo }}</label>

                                    @if ($pergunta->tipo === 'texto_curto')
                                    <input type="text" class="form-control form-salvar-estado"
                                        name="respostas[{{ $pergunta->id }}]">
                                    @elseif ($pergunta->tipo === 'texto_longo')
                                    <textarea class="form-control form-salvar-estado" rows="3"
                                        name="respostas[{{ $pergunta->id }}]"></textarea>
                                    @endif
                                    {{-- Adicione os outros tipos (radio, checkbox, select) aqui conforme fizemos na
                                    resposta anterior. Lembre-se de adicionar a classe 'form-salvar-estado' nos inputs
                                    --}}
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
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const submissaoId = "{{ $submissao->id }}"; // Usado para criar uma chave única de salvamento
    const chaveStorage = `rascunho_form_${submissaoId}`;

    // --- 1. LÓGICA DE NAVEGAÇÃO DO WIZARD ---
    const abas = document.querySelectorAll('#wizardTabs .nav-link');
    const conteudos = document.querySelectorAll('.tab-pane');
    
    document.querySelectorAll('.btn-proximo').forEach((btn, index) => {
        btn.addEventListener('click', () => {
            // Remove a classe ativa da aba/conteúdo atual
            abas[index].classList.remove('active');
            conteudos[index].classList.remove('show', 'active');
            
            // Ativa a próxima aba/conteúdo
            abas[index + 1].removeAttribute('disabled'); // Libera o clique na próxima aba
            abas[index + 1].classList.add('active');
            conteudos[index + 1].classList.add('show', 'active');
        });
    });

    document.querySelectorAll('.btn-anterior').forEach((btn, index) => {
        btn.addEventListener('click', () => {
            // O index do botão anterior é relativo à aba atual (ignorando a primeira que não tem botão)
            let abaAtual = index + 1;
            
            abas[abaAtual].classList.remove('active');
            conteudos[abaAtual].classList.remove('show', 'active');
            
            abas[abaAtual - 1].classList.add('active');
            conteudos[abaAtual - 1].classList.add('show', 'active');
        });
    });

    // --- 2. LÓGICA DE SALVAMENTO DE ESTADO (AUTO-SAVE NO NAVEGADOR) ---
    const formWizard = document.getElementById('formWizard');
    const inputsEstado = document.querySelectorAll('.form-salvar-estado');

    // Recupera os dados salvos ao carregar a página
    const dadosSalvos = JSON.parse(localStorage.getItem(chaveStorage)) || {};
    
    inputsEstado.forEach(input => {
        // Se existir valor salvo, preenche o input
        if (dadosSalvos[input.name]) {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = (input.value === dadosSalvos[input.name]);
            } else {
                input.value = dadosSalvos[input.name];
            }
        }

        // Fica escutando as mudanças para salvar em tempo real
        input.addEventListener('change', (e) => {
            dadosSalvos[e.target.name] = e.target.value;
            localStorage.setItem(chaveStorage, JSON.stringify(dadosSalvos));
        });
    });

    // Limpa o cache quando o formulário for enviado com sucesso
    formWizard.addEventListener('submit', () => {
        localStorage.removeItem(chaveStorage);
    });
});
</script>
@endsection