<?php
require_once '../conexoes/conexao.php';
require_once '../conexoes/funcoes.php';

session_start();

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: dashboard.php");
    exit();
}

$id = (int) $id;

// Verifica se a notícia pertence ao usuário logado
$stmt = $conn->prepare("SELECT * FROM noticias WHERE id = ? AND autor = ?");
$stmt->execute([$id, $_SESSION['usuario_id']]);
$noticia = $stmt->fetch();

if (!$noticia) {
    header("Location: dashboard.php");
    exit();
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = sanitizaEntrada($_POST['titulo'] ?? '');
    $conteudo = sanitizaEntrada($_POST['noticia'] ?? '');
    $imagem = $_FILES['imagem'] ?? null;

    if (empty($titulo) || empty($conteudo)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        $imagem_path = $noticia['imagem'];

        // Se nova imagem enviada
        if ($imagem && $imagem['error'] === 0) {
            $nova = uploadImagem($imagem);
            if ($nova) {
                // Remove a anterior
                if ($imagem_path && file_exists($imagem_path)) {
                    unlink($imagem_path);
                }
                $imagem_path = $nova;
            } else {
                $erro = "Erro ao fazer upload da imagem.";
            }
        }

        if (!$erro) {
            $stmt = $conn->prepare("UPDATE noticias SET titulo = ?, noticia = ?, imagem = ? WHERE id = ? AND autor = ?");
            if ($stmt->execute([$titulo, $conteudo, $imagem_path, $id, $_SESSION['usuario_id']])) {
                $sucesso = "Notícia atualizada com sucesso!";
                $noticia['titulo'] = $titulo;
                $noticia['noticia'] = $conteudo;
                $noticia['imagem'] = $imagem_path;
            } else {
                $erro = "Erro ao atualizar a notícia.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Notícia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Portal de Notícias</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Painel</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4 text-center">Editar Notícia</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php elseif ($sucesso): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required value="<?= htmlspecialchars($noticia['titulo']) ?>">
        </div>

        <div class="mb-3">
            <label for="noticia" class="form-label">Conteúdo</label>
            <textarea name="noticia" id="noticia" class="form-control" rows="8" required><?= htmlspecialchars($noticia['noticia']) ?></textarea>
        </div>

        <?php if ($noticia['imagem']): ?>
            <div class="mb-3">
                <label class="form-label">Imagem Atual</label><br>
                <img src="<?= htmlspecialchars($noticia['imagem']) ?>" alt="Imagem atual" class="img-thumbnail" style="max-height: 200px;">
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="imagem" class="form-label">Nova Imagem (opcional)</label>
            <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
