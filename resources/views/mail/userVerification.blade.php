<!-- <!DOCTYPE html>
<html lang="pl">
<body>

<div>
    <h1>User Email Verification</h1>
    <p>Hello {{ $user->first_name }}</p>
    <p>Please click the button below to verify your email address:</p>
    <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: #fff; text-decoration: none;">Click to verify</a>
</div>

</body>
</html> -->


<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Email Confirmation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style type="text/css">
    @media screen {
      @font-face {
        font-family: 'Source Sans Pro';
        font-style: normal;
        font-weight: 400;
        src: local('Source Sans Pro Regular'), local('SourceSansPro-Regular'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe03otPbuUS0.woff) format('woff');
      }

      @font-face {
        font-family: 'Source Sans Pro';
        font-style: normal;
        font-weight: 700;
        src: local('Source Sans Pro Bold'), local('SourceSansPro-Bold'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/toadOcfmlt9b38dHJxOBGFkQc6VGVFSmCnC_l7QZG60.woff) format('woff');
      }
    }


    body,
    table,
    td,
    a {
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
    }

    table,
    td {
      mso-table-rspace: 0pt;
      mso-table-lspace: 0pt;
    }


    img {
      -ms-interpolation-mode: bicubic;
    }


    a[x-apple-data-detectors] {
      font-family: inherit !important;
      font-size: inherit !important;
      font-weight: inherit !important;
      line-height: inherit !important;
      color: inherit !important;
      text-decoration: none !important;
    }


    div[style*="margin: 16px 0;"] {
      margin: 0 !important;
    }

    body {
      width: 100% !important;
      height: 100% !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    table {
      border-collapse: collapse !important;
    }

    a {
      color: #1a82e2;
    }

    img {
      height: auto;
      line-height: 100%;
      text-decoration: none;
      border: 0;
      outline: none;
    }


    .logo {
      font-family: 'Source Sans Pro';
      font-size: 58px;
      font-weight: bold;
      color: #1a82e2;
      margin: 0;
      padding: 0;
    }
  </style>

</head>

<body style="background-color: #e9ecef;">

  <table border="0" cellpadding="0" cellspacing="0" width="100%">

    <tr>
      <td align="center" bgcolor="#e9ecef">

        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
          <tr>
            <td align="center" valign="top" style="padding: 36px 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif;">
              <h1 class="logo">Tradely</h1>
            </td>
          </tr>
        </table>

      </td>
    </tr>

    <tr>
      <td align="center" bgcolor="#e9ecef">

        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
          <tr>
            <td align="center" bgcolor="#ffffff"
              style="padding: 36px 24px 0; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; border-top: 3px solid #d4dadf;">
              <h1
                style="margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -1px; line-height: 48px; color: #666;">
                Potwierdź adres email</h1>
            </td>
          </tr>
        </table>

      </td>
    </tr>

    <tr>
      <td align="center" bgcolor="#e9ecef">

        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

          <tr>
            <td align="left" bgcolor="#ffffff"
              style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p>Witaj {{$user->login}},</p>
              <p style="margin: 0;">Wciśnij przycisk poniżej aby potwierdzić swój adres email i zacząć korzystać z
                aplikacji Tradely</p>
            </td>
          </tr>

          <tr>
            <td align="left" bgcolor="#ffffff">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td align="center" bgcolor="#ffffff" style="padding: 12px;">
                    <table border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td align="center" bgcolor="#1a82e2" style="border-radius: 6px;">
                          <a href="{{ $verificationUrl }}" target="_blank"
                            style="display: inline-block; padding: 16px 36px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;">Aktywuj
                            swoje konto</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td align="left" bgcolor="#ffffff"
              style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p style="margin: 0;">Możesz również kliknąć podany link:</p>
              <p style="margin: 0;"><a href="{{ $verificationUrl }}" target="_blank">{{ $verificationUrl }}</a></p>
            </td>
          </tr>

          <tr>
            <td align="left" bgcolor="#ffffff"
              style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; border-bottom: 3px solid #d4dadf">
              <p style="margin: 0;">Pozdrawiamy,<br> Tradely</p>
            </td>
          </tr>

        </table>

      </td>
    </tr>

    <tr>
      <td align="center" bgcolor="#e9ecef" style="padding: 24px;">

        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

          <!-- start permission -->
          <tr>
            <td align="center" bgcolor="#e9ecef"
              style="padding: 12px 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; color: #666;">
              <p style="margin: 0;">Otrzymujesz tego e-maila, ponieważ otrzymaliśmy prośbę o aktywację
                Twojego konta. Jeśli tworzyłeś konta w aplikacji Tradely, możesz bezpiecznie usunąć tego e-maila.</p>
            </td>
          </tr>

          <tr>
            <td align="center" bgcolor="#e9ecef"
              style="padding: 12px 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; color: #666;">
              <p style="margin: 0;">Zarząd Tradely</p>
            </td>
          </tr>

        </table>

      </td>
    </tr>

  </table>

</body>

</html>