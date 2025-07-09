<?php
session_start();
require_once './conexoes/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID inválido.";
    exit;
}

$id = (int)$_GET['id'];

// Buscar anúncio atual
$stmt = $conn->prepare("SELECT * FROM anuncio WHERE id = ?");
$stmt->execute([$id]);
$anuncio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anuncio) {
    echo "Anúncio não encontrado.";
    exit;
}

// Atualizar anúncio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $texto = $_POST['texto'] ?? '';
    $link = $_POST['link'] ?? '';
    $imagem = $_POST['imagem'] ?? '';
    $valorAnuncio = $_POST['valorAnuncio'] ?? 0;
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE anuncio SET nome=?, texto=?, link=?, imagem=?, valorAnuncio=?, destaque=?, ativo=? WHERE id=?");
    $stmt->execute([$nome, $texto, $link, $imagem, $valorAnuncio, $destaque, $ativo, $id]);

    header("Location: listar_anuncios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Anúncio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f2027;
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            margin-top: 40px;
            max-width: 700px;
        }
        .form-control, .form-check-label {
            background-color: #1e2227;
            color: #c0e7ff;
            border: 1px solid #00c6ff;
        }
        .btn-primary {
            background: #00c6ff;
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Editar Anúncio</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($anuncio['nome']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Texto</label>
            <textarea name="texto" class="form-control" required rows="4"><?= htmlspecialchars($anuncio['texto']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Link</label>
            <input type="url" name="link" class="form-control" required value="<?= htmlspecialchars($anuncio['link']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">URL da Imagem</label>
            <input type="url" name="imagem" class="form-control" value="<?= htmlspecialchars($anuncio['imagem']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Valor do Anúncio</label>
            <input type="number" name="valorAnuncio" class="form-control" step="0.01" value="<?= htmlspecialchars($anuncio['valorAnuncio']) ?>">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="destaque" id="destaque" <?= $anuncio['destaque'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="destaque">Destaque</label>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="ativo" id="ativo" <?= $anuncio['ativo'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="ativo">Ativo</label>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="listar_anuncios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
