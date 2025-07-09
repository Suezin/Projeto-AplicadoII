<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../conexoes/conexao.php';
require_once '../classes/Usuario.php';

$usuarioObj = new Usuario($conn);
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
 
    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos!';
    } else {
        $usuario = $usuarioObj->login($email, $senha);
      
        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_sexo'] = $usuario['sexo'];
       
            header('Location: ../index.php');
            exit(); // Importante para interromper a execução após o redirecionamento
        } else {
            $erro = 'E-mail ou senha incorretos!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Login - Portal Inovação & Tecnologia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #0f2027, #2c5364, #00c6ff);
            min-height: 100vh;
        }

        .login-container {
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .tech-title {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #0f2027;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .form-label {
            color: #2c5364;
        }

        .btn-primary {
            background: linear-gradient(90deg, #00c6ff 0%, #0072ff 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #0072ff 0%, #00c6ff 100%);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2 class="text-center mb-4 tech-title">Autenticação</h2>
        <p class="text-center mb-4" style="color:#2c5364;">Portal de Inovação & Tecnologia</p>

        <?php if ($erro): ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Entrar</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="cadastro.php">Criar nova conta</a>
            <div class="mt-2">
                <a href="redefinir_senha.php">Esqueci minha senha</a>
            </div>
        </div>
    </div>
</body>

</html>
