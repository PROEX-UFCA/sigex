<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #F5F5F5; padding: 20px; }
        .container { background-color: white; border-top: 5px solid #50BDCA; padding: 30px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; background-color: #50BDCA; color: #24130d; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        h2 { color: #472519; }
        p { color: #532B1D; font-size: 16px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Parceria Finalizada! 🌟</h2>
        <p>Olá, <strong>{{ $instituicaoName }}</strong>!</p>
        <p>A sua parceria com o projeto <strong>{{ $acaoTitulo }}</strong> foi concluída com sucesso.</p>
        <p>Agradecemos muito pela colaboração! Por hora, o vínculo foi finalizado no sistema, mas caso surja novamente o interesse neste projeto ou em desenvolver algo novo com a equipe, não hesite em contatá-los.</p>
        
        <a href="mailto:{{ $coordenadorEmail }}?subject=Nova%20Parceria:%20{{ urlencode($instituicaoName) }}" class="btn">
            Contatar {{ $coordenadorName }}
        </a>
    </div>
</body>
</html>