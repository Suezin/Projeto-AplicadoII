<?php
session_start();
include_once '../conexoes/conexao.php';
include_once '../classes/Usuario.php';


$mensagem = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $usuario = new Usuario($conn);
    $codigo = $usuario->gerarCodigoVerificacao($email);

    if ($codigo) {
        $mensagem = "Seu código de verificação é: $codigo. Por favor, anote o código e <a href='./esqueci_senha.php'>clique aqui</a> para redefinir sua senha.";
    } else {
        $mensagem = 'E-mail não encontrado.';
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
</head>
<style>
    body {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        color: #f1f1f1;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 1rem;
    }

    .recuperar-box {
        background: #1b1f23;
        padding: 2.5rem;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 198, 255, 0.15);
        width: 100%;
        max-width: 460px;
        text-align: center;
    }

    h1 {
        color: #00c6ff;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        border-left: 5px solid #00c6ff;
        padding-left: 10px;
        text-align: left;
        text-shadow: 1px 1px 2px rgba(0, 198, 255, 0.6);
    }

    form {
        margin-top: 1rem;
    }

    label {
        display: block;
        text-align: left;
        margin-bottom: 0.3rem;
        color: #c0e7ff;
        font-weight: 500;
    }

    input[type="email"] {
        width: 100%;
        padding: 0.6rem;
        border: 1px solid #0072ff;
        border-radius: 6px;
        background-color: #2c3e50;
        color: #f1f1f1;
        margin-bottom: 1rem;
    }

    input[type="email"]:focus {
        outline: none;
        border-color: #00c6ff;
        box-shadow: 0 0 5px #00c6ff;
    }

    input[type="submit"] {
        width: 100%;
        padding: 0.6rem;
        background: linear-gradient(90deg, #00c6ff, #0072ff);
        border: none;
        color: white;
        font-weight: bold;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    input[type="submit"]:hover {
        background: linear-gradient(90deg, #0072ff, #00c6ff);
    }

    p {
        margin-top: 1rem;
        font-size: 0.95rem;
        color: #c0e7ff;
    }

    a {
        color: #00c6ff;
        margin-top: 1.2rem;
        display: inline-block;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

<body>
    <div class="recuperar-box">
        <h1>Recuperar Senha</h1>

        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            <input type="submit" value="Enviar">
        </form>

        <p><?php echo $mensagem; ?></p>
        <a href="../index.php">← Voltar ao Início</a>
    </div>
</body>