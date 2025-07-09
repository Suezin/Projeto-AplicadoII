<?php
session_start();

require_once './conexoes/conexao.php';
require_once './conexoes/funcoes.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizaEntrada($_POST['nome'] ?? '');
    $descricao = sanitizaEntrada($_POST['descricao'] ?? '');
    $link = sanitizaEntrada($_POST['link'] ?? '');
    $valor = sanitizaEntrada($_POST['valor'] ?? '');
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $imagem = $_FILES['imagem'] ?? null;

    if (empty($nome) || empty($descricao) || empty($link) || empty($valor) || !$imagem || $imagem['error'] !== 0) {
        $erro = "Por favor, preencha todos os campos obrigatórios e envie uma imagem válida.";
    } else {
        $pasta_destino = 'uploads/';
        if (!is_dir($pasta_destino)) {
            mkdir($pasta_destino, 0777, true);
        }

        $nome_arquivo = uniqid() . '_' . basename($imagem['name']);
        $caminho_arquivo = $pasta_destino . $nome_arquivo;

        if (move_uploaded_file($imagem['tmp_name'], $caminho_arquivo)) {
            $imagem_path = $caminho_arquivo;

            $stmt = $conn->prepare("INSERT INTO anuncio(nome, imagem, link, texto, ativo, destaque, data_cadastro, valorAnuncio) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
            if ($stmt->execute([$nome, $imagem_path, $link, $descricao, 1, $destaque, $valor])) {
                $sucesso = "Anúncio cadastrado com sucesso!";
            } else {
                $erro = "Erro ao salvar no banco de dados.";
            }
        } else {
            $erro = "Erro ao fazer upload da imagem.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Anúncio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4 text-center">Cadastrar Novo Anúncio</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php elseif ($sucesso): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nome" class="form-label">Título do Anúncio</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="valor" class="form-label">Preço (valor do anúncio)</label>
            <input type="text" name="valor" id="valor" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">Link do Anúncio</label>
            <input type="text" name="link" id="link" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem</label>
            <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*" required>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="destaque" id="destaque">
            <label class="form-check-label" for="destaque">Marcar como Destaque</label>
        </div>

        <button type="submit" class="btn btn-primary">Cadastrar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>
</body>
</html>
