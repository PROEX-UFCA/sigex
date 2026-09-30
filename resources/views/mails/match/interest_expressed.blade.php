<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Alegreya', Arial, sans-serif; background-color: #F5F5F5; padding: 20px; }
        .container { background-color: white; border-top: 5px solid #F5BE56; padding: 30px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; background-color: #F5BE56; color: #24130d; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        h2 { color: #472519; }
        p { color: #532B1D; font-size: 16px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Novo Interesse de Parceria! 🤝</h2>
        <p>Olá, <strong>{{ $coordenadorName }}</strong>!</p>
        <p>A instituição <strong>{{ $instituicaoName }}</strong> acessou a vitrine do SIGEx e demonstrou interesse em firmar uma parceria com o seu projeto: <strong>{{ $acaoTitulo }}</strong>.</p>
        
        <p>Acesse o painel do sistema para confirmar a parceria e entre em contato diretamente com a instituição utilizando o botão abaixo:</p>
        
        <a href="mailto:{{ $instituicaoEmail }}?subject=Sobre%20o%20Interesse%20na%20Ação:%20{{ urlencode($acaoTitulo) }}" class="btn">
            Enviar email para <strong>{{ $instituicaoName }}</strong>
        </a>
    </div>
</body>
</html>