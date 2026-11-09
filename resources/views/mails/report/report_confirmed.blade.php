<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Alegreya', Arial, sans-serif;
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
        <div class="header">Recebemos seu relatório, obrigado!</div>
        <div class="content">
            <p>Olá, {{ $data['nome'] }}</p>
            <p>Recebemos seu relatório, agradecemos pelo seu envio.</p>

            <div class="info-box">
                <p style="margin: 0 0 8px 0;"><strong>Relatório:</strong> {{ $data['titulo'] }}</p>
                <p style="margin: 0 0 8px 0;"><strong>Finalizado em:</strong> {{ $data['data'] }}</p>
            </div>
        </div>
    </div>
</body>

</html>
