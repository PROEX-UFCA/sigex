@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">
    <div class="m-0 p-0 mb-3 row">
        <div class="btn-list col-12 col-md-6 p-0 m-0">
            <a href="{{route('report.monitor', $submissao->id_relatorio)}}" class="btn">Voltar página</a>
        </div>
    </div>

</div>
@endsection

@section('content')
<div class="page-body row">
    <div class="m-0 p-0 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 text-primary fw-bold">Validação: {{ $submissao->relatorio->titulo }}</h3>
            <p class="mb-0 text-muted">Avaliando submissão #{{ $submissao->id }}</p>
        </div>
        <a href="{{route('report.monitor', $submissao->id_relatorio)}}" class="btn">Voltar página</a>
    </div>

    <div class="row p-0">
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="col text-truncate p-3">
                    <p class="mb-1 text-reset d-block">Título da ação</p>
                    <div class="d-block text-secondary text-truncate mt-n1" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                        data-bs-title="{{$submissao->acao->titulo}}">
                        {{$submissao->acao->titulo}}
                    </div>
                </div>
                <hr class="my-0">
                <div class="col text-truncate p-3">
                    <p class="mb-1 text-reset d-block">Modalidade</p>
                    <div class="d-block text-secondary text-truncate mt-n1" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                        data-bs-title="{{$submissao->acao->modalidade_edital}}">
                        {{$submissao->acao->modalidade_edital}}
                    </div>
                </div>
                <hr class="my-0">
                <div class="col text-truncate p-3">
                    <p class="mb-1 text-reset d-block">Tipo</p>
                    <div class="d-block text-secondary text-truncate mt-n1" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                        data-bs-title="{{$submissao->acao->tipo_acao}}">
                        {{$submissao->acao->tipo_acao}}
                    </div>
                </div>
                <hr class="my-0">
                <div class="col text-truncate p-3">
                    <p class="mb-1 text-reset d-block">Área temática</p>
                    <div class="d-block text-secondary text-truncate mt-n1" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                        data-bs-title="{{$submissao->acao->area_tematica}}">
                        {{$submissao->acao->area_tematica}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-9 mt-3 mt-md-0">
            <div class="m-0 h-100">
                {{-- // {{ route('report.validate.store', $submissao->id) }} --}}
                <form action="" method="POST">
                    @csrf

                    @foreach($secoes as $secao)
                    <div class="card shadow-sm mb-4 border-top border-primary border-3">
                        <div class="card-header bg-white pb-2">
                            <h4 class="mb-1 fw-bold text-dark">{{ $secao['titulo'] }}</h4>
                            <p class="mb-0 text-muted small">{{ $secao['descricao'] }}</p>
                        </div>

                        <div class="card-body p-4">
                            @foreach($secao['perguntas'] as $pergunta)
                            <div class="pergunta-validacao mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="row">
                                    {{-- LADO ESQUERDO: A Resposta do Usuário --}}
                                    <div class="col-12 col-md-7 mb-3 mb-md-0">
                                        <label class="fw-bold mb-2 text-dark fs-5">{{ $pergunta['enunciado']
                                            }}</label>

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
                                                        {{-- Usamos {!! !!} porque formatarResposta pode retornar
                                                        HTML (como links
                                                        de arquivo) --}}
                                                        <td>{!! $coluna['valor'] !!}</td>
                                                        @endforeach
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif
                                        @else
                                        <div class="p-3 bg-light rounded border">
                                            {{-- Usamos {!! !!} para renderizar a tag <a> do arquivo caso seja file
                                                --}}
                                                <span class="fs-6">{!! $pergunta['valor'] !!}</span>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- LADO DIREITO: Avaliação e Feedback --}}
                                    <div class="col-12 col-md-5 border-start">
                                        <div class="px-3">
                                            <p class="fw-bold mb-2 small text-uppercase text-muted">Ação do
                                                Avaliador</p>

                                            <div class="d-flex gap-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input radio-status" type="radio"
                                                        name="validacao[{{ $pergunta['id'] }}][status]"
                                                        id="aprovar_{{ $pergunta['id'] }}" value="aprovado" required>
                                                    <label class="form-check-label text-success fw-semibold"
                                                        for="aprovar_{{ $pergunta['id'] }}">
                                                        <i class="ti ti-check"></i> Válido
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input radio-status" type="radio"
                                                        name="validacao[{{ $pergunta['id'] }}][status]"
                                                        id="reprovar_{{ $pergunta['id'] }}" value="correcao">
                                                    <label class="form-check-label text-danger fw-semibold"
                                                        for="reprovar_{{ $pergunta['id'] }}">
                                                        <i class="ti ti-x"></i> Solicitar Correção
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="feedback-box mt-2" style="display: none;">
                                                <textarea class="form-control form-control-sm"
                                                    name="validacao[{{ $pergunta['id'] }}][feedback]" rows="2"
                                                    placeholder="Descreva o motivo ou o que precisa ser corrigido..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <div class="card shadow-sm mt-4">
                        <div class="card-body d-flex justify-content-between align-items-center bg-light">
                            <span class="text-muted">Certifique-se de preencher a avaliação de todos os itens antes
                                de
                                enviar.</span>
                            <button type="submit" class="btn btn-success btn-lg">Concluir Avaliação</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@endsection