<?php
session_start();
require_once '../config/config.php';
require_once '../classes/Usuario.php';

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoDigitado = $_POST['codigo'] ?? '';
    $usuario = new Usuario($db);
    $dadosUsuario = $usuario->verificarCodigo($codigoDigitado);

    if ($dadosUsuario) {
        $_SESSION['codigo_verificacao'] = $codigoDigitado;
        header("Location: redefinir_senha.php");
        exit();
    } else {
        $mensagem = "Código inválido. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Verificar Código</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            min-height: 100vh;
        }
        .form-container {
            max-width: 450px;
            margin: 80px auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2 class="text-center mb-4">Verificar Código</h2>
    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-danger"><?= $mensagem ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="codigo">Digite o código enviado:</label>
            <input type="text" name="codigo" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Verificar Código</button>
    </form>
</div>
</body>
</html>
