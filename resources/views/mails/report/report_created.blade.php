<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #e0e0e0;
        }

        .header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #206bc4;
        }

        .content {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #206bc4;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #206bc4;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .footer {
            font-size: 12px;
            color: #777;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header">Novo Relatório Disponível</div>
        <div class="content">
            <p>Olá, {{ $data['name'] }}</p>
            <p>Um novo relatório foi disponibilizado e requer seu preenchimento.</p>

            <div class="info-box">
                <p style="margin: 0 0 8px 0;"><strong>Relatório:</strong> {{ $data['titulo_relatorio'] }}</p>
                @if(!empty($data['nome_acao']))
                <p style="margin: 0;"><strong>Ação:</strong> {{ $data['nome_acao'] }}</p>
                @endif
                <p style="margin: 0 0 8px 0;"><strong>Data de início:</strong> {{ $data['data_inicio'] }}</p>
                <p style="margin: 0 0 8px 0;"><strong>Prazo para preenchimento:</strong> {{ $data['prazo'] }}</p>
            </div>

            <p>Clique no botão abaixo para acessar a plataforma e preencher os dados solicitados:</p>

            <div style="text-align: center; margin-top: 25px;">
                <a href="{{ $data['url'] }}" class="btn" target="_blank">Acessar Relatório</a>
            </div>
        </div>
        <div class="footer">
            <p>Se você não conseguir clicar no botão, copie e cole o link no seu navegador:</p>
            <p><a href="{{ $data['url'] }}">{{ $data['url'] }}</a></p>
        </div>
    </div>
</body>

</html>