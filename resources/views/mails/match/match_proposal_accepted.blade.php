<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #F5F5F5; padding: 20px; }
        .container { background-color: white; border-top: 5px solid #11C76F; padding: 30px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; background-color: #11C76F; color: white; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        h2 { color: #333F4C; }
        p { color: #4E5964; font-size: 16px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Proposta de Parceria Aceita! 🤝</h2>
        <p>Olá, <strong>{{ $coordenadorName }}</strong>!</p>
        <p>A instituição <strong>{{ $instituicaoName }}</strong> aceitou o seu convite de parceria para o projeto <strong>{{ $acaoTitulo }}</strong>.</p>
        
        <p>Para dar continuidade aos próximos passos, entre em contato diretamente com a instituição clicando no botão abaixo:</p>
        
        <a href="mailto:{{ $instituicaoEmail }}?subject=Parceria:%20{{ urlencode($acaoTitulo) }}" class="btn">
            Enviar Email para {{ $instituicaoName }}
        </a>
    </div>
</body>
</html>