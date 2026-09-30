<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Alegreya', Arial, sans-serif; background-color: #F5F5F5; padding: 20px; }
        .container { background-color: white; border-top: 5px solid #75b747; padding: 30px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; background-color: #75b747; color: white; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        h2 { color: #472519; }
        p { color: #532B1D; font-size: 16px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Parceria Confirmada! 🤝</h2>
        <p>Olá, <strong>{{ $instituicaoName }}</strong>!</p>
        <p>A coordenação do projeto <strong>{{ $acaoTitulo }}</strong> aceitou sua demonstração de interesse e deseja firmar uma parceria.</p>
        
        <p>Para dar continuidade aos próximos passos, entre em contato diretamente com a coordenação clicando no botão abaixo:</p>
        
        <a href="mailto:{{ $coordenadorEmail }}?subject=Parceria:%20{{ urlencode($acaoTitulo) }}" class="btn">
            Enviar Email para {{ $coordenadorName }}
        </a>
    </div>
</body>
</html>