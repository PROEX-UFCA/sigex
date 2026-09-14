<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastrar senha</title>
</head>

<body style="margin: 0; padding: 0; background-color: #F5F5F5; font-family: Arial, Helvetica, sans-serif;">

  <!-- Tabela Container Principal -->
  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F5F5F5; padding: 20px 0;">
    <tr>
      <td align="center">

        <!-- Tabela do Card Centralizado (Max-width 600px para emails) -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

          <!-- Cabeçalho -->
          <tr>
            <td align="center" style="padding: 10px 0 20px 0;">
              <h1 style="color: #11C76F; font-size: 24px; margin: 0; font-family: Arial, Helvetica, sans-serif;">
                Primeiro acesso
              </h1>
            </td>
          </tr>

          <!-- Corpo da Mensagem -->
          <tr>
            <td bgcolor="#FFFFFF"
              style="background-color: #ffffff; border-top: 6px solid #11C76F; padding: 30px; font-family: Arial, Helvetica, sans-serif;">

              <h2 style="color: #333F4C; font-size: 22px; margin: 0 0 15px 0;">
                Credenciais para o primeiro acesso!
              </h2>

              <!-- Divisor -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td style="border-bottom: 1px solid #EAEAEF; font-size: 1px; line-height: 1px; padding-bottom: 10px;">
                    &nbsp;</td>
                </tr>
              </table>

              <!-- Dados de Acesso -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 15px;">
                <tr>
                  <td style="color: #4E5964; font-size: 16px; padding: 5px 0;">
                    Email: <strong style="color: #333F4C;">{{ $email }}</strong>
                  </td>
                </tr>
                <tr>
                  <td style="color: #4E5964; font-size: 16px; padding: 5px 0;">
                    Senha: <strong style="color: #333F4C;">{{ $password }}</strong>
                  </td>
                </tr>
              </table>

              <!-- Divisor -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 15px;">
                <tr>
                  <td style="border-bottom: 1px solid #EAEAEF; font-size: 1px; line-height: 1px; padding-top: 10px;">
                    &nbsp;</td>
                </tr>
              </table>

              <!-- Texto Informativo -->
              <p style="color: #4E5964; font-size: 16px; line-height: 1.5; margin: 0 0 15px 0;">
                Olá, <span style="color: #333F4C; font-weight: bold;">{{ $name }}</span>!<br><br>
                Você foi cadastrado no nosso sistema no horário <span style="color: #333F4C; font-weight: bold;">{{
                  $time }}</span>.<br><br>
                Acesse o link abaixo para acessar o sistema!
              </p>

              <!-- Divisor -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td style="border-bottom: 1px solid #EAEAEF; font-size: 1px; line-height: 1px; padding-bottom: 15px;">
                    &nbsp;</td>
                </tr>
              </table>

              <!-- Botão de Ação -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 25px;">
                <tr>
                  <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td align="center" bgcolor="#11C76F" style="border-radius: 25px;">
                          <a href="{{ route('login') }}" target="_blank"
                            style="font-size: 16px; font-family: Arial, Helvetica, sans-serif; color: #ffffff; text-decoration: none; border-radius: 25px; padding: 12px 35px; display: inline-block; font-weight: bold;">
                            Acessar
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>

</html>