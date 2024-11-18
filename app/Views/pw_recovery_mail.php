<!DOCTYPE html>
<html>

<head>
    <title>Redefinição de Senha</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #007bff;
            text-align: center;
        }

        p {
            line-height: 1.6;
        }

        .button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Redefinição de Senha</h1>
        <p>Olá <?php echo esc($name) ?>,</p>
        <p>Você solicitou a redefinição da sua senha. Para criar uma nova senha, clique no botão abaixo:</p>
        <a href="<?php echo site_url("reset-confirm/$reset_token") ?>" class="button">Redefinir Senha</a>
        <p>Este link tem validade de 2 horas.</p>
        <p>Se você não solicitou a redefinição de senha, ignore este e-mail. Sua senha permanecerá inalterada.</p>
    </div>
</body>

</html>