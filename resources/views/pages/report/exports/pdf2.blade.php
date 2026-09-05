<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Relatório de Submissão' }}</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            font-size: 11px;
            color: #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        td,
        th {
            font-size: 11px;
            border: 1px solid #333;
            padding: 7px 9px;
            vertical-align: top;
        }

        /* Cabeçalho do Documento */
        .header-table th {
            text-align: center;
            background-color: #f4f4f4;
        }

        /* Título de Seção */
        .section-header {
            background-color: #add8e6;
            color: #000;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        .section-description {
            background-color: #f9f9f9;
            font-style: italic;
            color: #555;
            font-size: 10px;
        }

        /* Colunas Pergunta x Resposta */
        .question-col {
            width: 35%;
            background-color: #f4f6f8;
            font-weight: bold;
            color: #333;
        }

        .answer-col {
            width: 65%;
            background-color: #ffffff;
            word-wrap: break-word;
        }

        /* Badges de Status */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
        }

        .badge-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        /* Tabelas Internas (Perguntas do tipo Tabela) */
        .nested-table {
            width: 100%;
            margin-top: 4px;
            border-collapse: collapse;
        }

        .nested-table th,
        .nested-table td {
            font-size: 10px;
            padding: 5px;
            border: 1px solid #bbb;
        }

        .nested-table th {
            background-color: #e2e8f0;
            text-align: left;
        }

        .list-unstyled {
            padding-left: 15px;
            margin: 0;
        }

        .list-unstyled li {
            margin-bottom: 3px;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <!-- 1. CABEÇALHO DO RELATÓRIO -->
    <table class="header-table">
        <thead>
            <tr>
                <th width="20%">
                    <img src="{{ public_path('assets/img/illustrations/logo_proex_top.png') }}" width="100" alt="Logo">
                </th>
                <th width="80%">
                    <h2>{{ $title ?? 'Relatório de Submissão' }}</h2>
                </th>
            </tr>
        </thead>
    </table>

    <!-- 2. INFORMAÇÕES DA SUBMISSÃO -->
    @if (isset($submissao))
        <table>
            <thead>
                <tr class="section-header">
                    <th colspan="4" style="text-align: left; background-color: #2b5797; color: #fff;">
                        Dados da Submissão
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="20%"><strong>Responsável:</strong></td>
                    <td width="30%">{{ $submissao->user->name ?? ($submissao->usuario->name ?? 'Não informado') }}</td>
                    <td width="20%"><strong>Ação / Projeto:</strong></td>
                    <td width="30%">{{ $submissao->action->titulo ?? ($submissao->relatorio->titulo ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>Data Início:</strong></td>
                    <td>{{ isset($submissao->created_at) ? date('d/m/Y H:i', strtotime($submissao->created_at)) : '-' }}</td>
                    <td><strong>Status Finalização:</strong></td>
                    <td>
                        @if (!empty($submissao->finalizada_em))
                            <span class="badge badge-success">
                                Finalizado em {{ date('d/m/Y H:i', strtotime($submissao->finalizada_em)) }}
                            </span>
                        @else
                            <span class="badge badge-danger">Em andamento / Pendente</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- 3. LISTAGEM VERTICAL DE SEÇÕES, PERGUNTAS E RESPOSTAS -->
    @php
        // Suporta tanto o array $secoesData passado diretamente quanto o relacionamento $secoes
        $secoesLoop = $secoesData ?? ($secoes ?? ($submissao->relatorio->formulario->secoes ?? []));
    @endphp

    @foreach ($secoesLoop as $secao)
        @php
            $secaoTitulo = is_array($secao) ? $secao['titulo'] : $secao->titulo;
            $secaoDescricao = is_array($secao) ? $secao['descricao'] ?? null : $secao->descricao ?? null;
            $perguntas = is_array($secao) ? $secao['perguntas'] : $secao->perguntas;
        @endphp

        <table>
            <thead>
                <tr class="section-header">
                    <th colspan="2" style="text-align: left;">
                        {{ $secaoTitulo }}
                    </th>
                </tr>
                @if ($secaoDescricao)
                    <tr class="section-description">
                        <td colspan="2">
                            {{ $secaoDescricao }}
                        </td>
                    </tr>
                @endif
            </thead>
            <tbody>
                @forelse ($perguntas as $pergunta)
                    @php
                        // Extrai os campos se for Array ou Objeto
                        $pEnunciado = is_array($pergunta) ? $pergunta['enunciado'] : $pergunta->enunciado;
                        $pTipo = is_array($pergunta) ? $pergunta['tipo'] : $pergunta->tipo;
                        
                        // Busca o valor direto do array ou acessa o relacionamento respostas->valor
                        if (is_array($pergunta)) {
                            $pValor = $pergunta['valor'] ?? null;
                            $pValoresTabela = $pergunta['valores_tabela'] ?? [];
                        } else {
                            // Se for Model Eloquent: acessa $pergunta->respostas (hasMany) filtrado pela submissão
                            $respostaModel = $pergunta->respostas->where('id_submissao', $submissao->id ?? null)->first();
                            $pValor = $respostaModel ? $respostaModel->valor : null;
                            $pValoresTabela = [];
                        }
                    @endphp

                    <tr>
                        <!-- Coluna da Esquerda: Pergunta -->
                        <td class="question-col">
                            {{ $pEnunciado }}
                        </td>

                        <!-- Coluna da Direita: Resposta / Estrutura -->
                        <td class="answer-col">
                            {{-- CASO 1: TIPO TABELA DINÂMICA --}}
                            @if (in_array($pTipo, ['tabela', 'table']) || !empty($pValoresTabela))
                                @if (!empty($pValoresTabela))
                                    <table class="nested-table">
                                        <thead>
                                            <tr>
                                                <th width="6%">#</th>
                                                @php
                                                    // Pega os enunciados das colunas a partir da primeira linha
                                                    $primeiraLinha = $pValoresTabela[0] ?? [];
                                                @endphp
                                                @foreach ($primeiraLinha as $coluna)
                                                    <th>{{ is_array($coluna) ? $coluna['enunciado'] : $coluna->enunciado }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pValoresTabela as $indexLinha => $linha)
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold;">
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    @foreach ($linha as $celula)
                                                        @php
                                                            $valCelula = is_array($celula) ? $celula['valor'] : $celula->valor;
                                                        @endphp
                                                        <td>
                                                            @if ($valCelula === 'Não respondido' || is_null($valCelula))
                                                                <span style="color: #999; font-style: italic;">-</span>
                                                            @else
                                                                {{ $valCelula }}
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <em style="color: #888;">Sem registros na tabela.</em>
                                @endif

                            {{-- CASO 2: TIPO LOCALIZAÇÃO / ENDEREÇO --}}
                            @elseif (in_array($pTipo, ['location', 'localizacao', 'mapa']))
                                @php
                                    $locData = is_string($pValor) && str_starts_with($pValor, '{') ? json_decode($pValor, true) : $pValor;
                                @endphp

                                @if (is_array($locData))
                                    <div>
                                        <strong>Endereço:</strong> {{ $locData['endereco'] ?? ($locData['address'] ?? 'Não especificado') }}<br>
                                        @if (isset($locData['latitude']) && isset($locData['longitude']))
                                            <small style="color: #555;">Coordenadas: {{ $locData['latitude'] }}, {{ $locData['longitude'] }}</small>
                                        @endif
                                    </div>
                                @else
                                    {{ $pValor ?? 'Não respondido' }}
                                @endif

                            {{-- CASO 3: ARQUIVOS / DOCUMENTOS / IMAGENS --}}
                            @elseif (in_array($pTipo, ['file', 'arquivo', 'imagem', 'image']))
                                @if ($pValor && $pValor !== 'Não respondido')
                                    @php
                                        $ext = strtolower(pathinfo($pValor, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp

                                    @if ($isImage && file_exists(public_path($pValor)))
                                        <div style="margin-top: 3px;">
                                            <img src="{{ public_path($pValor) }}" style="max-width: 150px; max-height: 110px; border: 1px solid #ccc; padding: 2px;">
                                        </div>
                                    @else
                                        📎 <strong>Arquivo:</strong> {{ basename($pValor) }}
                                    @endif
                                @else
                                    <em style="color: #888;">Nenhum arquivo enviado</em>
                                @endif

                            {{-- CASO 4: CHECKBOX / MÚLTIPLA ESCOLHA --}}
                            @elseif (in_array($pTipo, ['checkbox', 'select_multiple']))
                                @php
                                    $itensArray = is_string($pValor) && str_starts_with($pValor, '[') ? json_decode($pValor, true) : null;
                                @endphp

                                @if (is_array($itensArray))
                                    <ul class="list-unstyled">
                                        @foreach ($itensArray as $item)
                                            <li>• {{ $item }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $pValor ?? 'Não respondido' }}
                                @endif

                            {{-- CASO PADRÃO: TEXTO, TEXTAREA, DATA, RADIO, SELECT --}}
                            @else
                                @if (is_null($pValor) || $pValor === '' || $pValor === 'Não respondido')
                                    <em style="color: #888;">Não respondido</em>
                                @else
                                    {!! nl2br(e($pValor)) !!}
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #888;">
                            Nenhuma pergunta registrada para esta seção.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

    <!-- RODAPÉ DO DOCUMENTO -->
    <div style="padding: 8px 0; font-size: 10px; text-align: right; border-top: 1px solid #ccc; margin-top: 20px;">
        Emitido por: {{ auth()->user()->name ?? ($usuario->name ?? 'Sistema') }} em {{ date('d/m/Y H:i') }}
    </div>
</body>

</html>