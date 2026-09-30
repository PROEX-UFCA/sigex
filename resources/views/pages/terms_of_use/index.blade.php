<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Proex - Termos de Uso</title>
  <!-- CSS files -->

  @yield('styles')

  <link href="{{ asset('assets/css/tabler-icons.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/tabler.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/tabler-payments.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/tabler-vendors.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/demo.min.css') }}" rel="stylesheet" />
  <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">

  <style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
      --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
      font-feature-settings: "cv03", "cv04", "cv11";
    }
  </style>
</head>

<body>
  <script src="{{ asset('assets/js/demo-theme.min.js?1684106062') }}"></script>
  <div class="page">
    <header class="navbar navbar-expand-md d-print-none">
      <div class="container-xl">
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
          <a href="/home"
            style="font: 600; font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif; color: #4a4a4a"
            class="d-inline-flex align-items-center justify-content-center text-decoration-none">
            <span class="bg-primary d-inline-flex align-items-center justify-content-center rounded-3 p-1 me-2">
              <i class="ti ti-clipboard-text text-white" style="font-size: 25px"></i>
            </span>
            Proex
          </a>
        </h1>
      </div>
    </header>
    <div class="page-wrapper">
      <div class="container my-5">
        <h2>Termos de Uso</h2>
        <p>Última atualização: 01/07/2025</p>

        <p>Ao utilizar esta plataforma, você concorda com os seguintes termos:</p>

        <ul>
          <li><strong>Uso da Plataforma:</strong> Esta plataforma visa organizar e recomendar conteúdos
            educacionais, bem como permitir a interação entre usuários. O uso é restrito a usuários cadastrados
            com e-mail institucional.</li>
          <li><strong>Conta do Usuário:</strong> O usuário é responsável por manter suas credenciais seguras. A
            violação de regras pode levar à suspensão da conta.</li>
          <li><strong>Conteúdo do Usuário:</strong> Informações como curso, turma e redes sociais são opcionais e
            podem ser usadas para exibir perfis e personalizar recomendações. O usuário pode apagar esses dados a
            qualquer momento.</li>
          <li><strong>Modificações:</strong> Este termo pode ser alterado a qualquer momento, com aviso prévio ao
            usuário.</li>
        </ul>

        <h2 class="mt-5">Política de Privacidade</h2>

        <ul>
          <li><strong>Dados Coletados:</strong> Coletamos nome, e-mail, IP, informações de login, e dados
            opcionais como curso, turma e redes sociais.</li>
          <li><strong>Finalidade:</strong> Usamos esses dados para análise de uso do sistema, recomendações
            personalizadas, e exibição na rede social interna.</li>
          <li><strong>Compartilhamento:</strong> Não compartilhamos seus dados com terceiros.</li>
          <li><strong>Segurança:</strong> Os dados são armazenados de forma segura e criptografada sempre que
            possível.</li>
          <li><strong>Direitos do Usuário:</strong> Você pode solicitar a visualização, correção ou exclusão de
            seus dados pessoais a qualquer momento.</li>
        </ul>

        <h4 class="mt-4">Registro de Atividades</h4>
        <p>
          Todas as ações realizadas pelos usuários dentro da plataforma poderão ser registradas em logs de atividade.
          Esses registros incluem o identificador do usuário, data e hora e tipo de ação realizada.
          A finalidade desses registros é garantir a segurança do sistema, permitir auditoria e melhorar a usabilidade
          da plataforma.
        </p>

      </div>

      <footer class="footer footer-transparent d-print-none">
        <div class="container-xl">
          <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-lg-auto ms-lg-auto">
              <ul class="list-inline list-inline-dots mb-0">
                <li class="list-inline-item"><a href="" target="_blank" class="link-secondary"
                    rel="noopener">Suport</a></li>
              </ul>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
              <ul class="list-inline list-inline-dots mb-0">
                <li class="list-inline-item">
                  <a href="#" class="link-secondary">Porex</a>&copy; 2026.
                  All rights reserved.
                </li>
                <li class="list-inline-item">
                  <a href="./changelog.html" class="link-secondary" rel="noopener">
                    v1.0.0-beta
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</body>

</html>
