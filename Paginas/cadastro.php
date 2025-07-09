<?php
session_start();
require_once '../conexoes/conexao.php';
require_once '../classes/Usuario.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $sexo = $_POST['sexo'] ?? 'O';
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif ($senha !== $confirmar) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        $usuario = new Usuario($conn);

        try {
            $usuario->criar($nome, $sexo, $email, $senha);
            $sucesso = 'Cadastro realizado com sucesso! Faça login.';
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro = 'E-mail já cadastrado.';
            } else {
                $erro = 'Erro ao cadastrar: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Portal Inovação & Tecnologia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #232526, #414345, #00c6ff);
            min-height: 100vh;
        }
        .cadastro-container {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
<div class="cadastro-container">
    <h2 class="text-center mb-4">Criar Conta</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php elseif ($sucesso): ?>
        <div class="alert alert-success"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome completo</label>
            <input type="text" class="form-control" name="nome" id="nome" required placeholder="Ex: João da Silva">
        </div>

        <div class="mb-3">
            <label for="sexo" class="form-label">Sexo</label>
            <select name="sexo" id="sexo" class="form-select">
                <option value="O">Outro / Prefiro não informar</option>
                <option value="M">Masculino</option>
                <option value="F">Feminino</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" name="email" id="email" required placeholder="exemplo@email.com">
        </div>

        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" name="senha" id="senha" required placeholder="Mínimo 6 letras ou números">
        </div>

        <div class="mb-3">
            <label for="confirmar_senha" class="form-label">Confirmar senha</label>
            <input type="password" class="form-control" name="confirmar_senha" id="confirmar_senha" required placeholder="Repita a senha acima">
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>
    </form>

    <div class="text-center mt-3">
        <a href="login.php">Já tenho conta</a>
    </div>
</div>
</body>
</html>
