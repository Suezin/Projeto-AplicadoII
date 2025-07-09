<?php
session_start();
require_once './conexoes/conexao.php';
require_once './classes/Usuario.php'; // ajuste o caminho se necessário

$usuario = new Usuario($conn);

$id = $_GET['id'] ?? null;
$dados = null;

if ($id) {
    $dados = $usuario->lerPorId($id); // Retorna array com chaves: nome, email, sexo
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $sexo = $_POST['sexo'] ?? '';

    if ($usuario->atualizar($id, $nome, $email, $sexo)) {
        header('Location: portal.php');
        exit;
    } else {
        $erro = "Erro ao atualizar o usuário.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Usuário</title>
</head>
<body>
    <h2>Editar Usuário</h2>

    <?php if (isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>

    <?php if ($dados): ?>
    <form method="post">
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($dados['nome']) ?>"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($dados['email']) ?>"><br><br>

        <label>Sexo:</label><br>
        <select name="sexo">
            <option value="M" <?= $dados['sexo'] === 'M' ? 'selected' : '' ?>>Masculino</option>
            <option value="F" <?= $dados['sexo'] === 'F' ? 'selected' : '' ?>>Feminino</option>
        </select><br><br>

        <button type="submit">Atualizar</button>
    </form>
    <?php else: ?>
        <p>Usuário não encontrado.</p>
    <?php endif; ?>
</body>
</html>