<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #F5F5F5; padding: 20px; }
        .container { background-color: white; border-top: 5px solid #FBC02D; padding: 30px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; background-color: #FBC02D; color: #333; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        h2 { color: #333F4C; }
        p { color: #4E5964; font-size: 16px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Convite de Parceria com a UFCA! 🤝</h2>
        <p>Olá, <strong>{{ $instituicaoName }}</strong>!</p>
        <p>A coordenação do projeto <strong>{{ $acaoTitulo }}</strong> (<strong>{{ $coordenadorName }}</strong>) visualizou o perfil da sua instituição na Vitrine e gostaria de firmar uma parceria.</p>
        
        <p>Você pode aceitar o convite diretamente no seu perfil da Vitrine ou entrar em contato com a coordenação agora mesmo clicando no botão abaixo:</p>
        
        <a href="mailto:{{ $coordenadorEmail }}?subject=Convite%20de%20Parceria:%20{{ urlencode($acaoTitulo) }}" class="btn">
            Enviar Email para {{ $coordenadorName }}
        </a>
    </div>
</body>
</html>