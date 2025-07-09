<?php
session_start();
require_once './conexoes/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM anuncio ORDER BY data_cadastro DESC");
$stmt->execute();
$anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Listar Anúncios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f2027;
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            margin-top: 40px;
        }
        table {
            background-color: #1e2227;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,198,255,0.3);
        }
        th, td {
            color: #c0e7ff;
        }
        .btn {
            margin-right: 5px;
        }
        .img-preview {
            max-width: 100px;
            max-height: 60px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Lista de Anúncios</h2>

    <a href="cadastrar_anuncio.php" class="btn btn-success mb-3">Novo Anúncio</a>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Resumo</th>
                <th>Imagem</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($anuncios as $anuncio): ?>
                <tr>
                    <td><?= htmlspecialchars($anuncio['id']) ?></td>
                    <td><?= htmlspecialchars($anuncio['nome']) ?></td>
                    <td><?= htmlspecialchars(substr($anuncio['texto'], 0, 50)) ?>...</td>
                    <td>
                        <?php if (!empty($anuncio['imagem'])): ?>
                            <img src="<?= htmlspecialchars($anuncio['imagem']) ?>" class="img-preview" alt="Imagem">
                        <?php else: ?>
                            Sem imagem
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($anuncio['data_cadastro'])) ?></td>
                    <td>
                        <a href="editar_anuncio.php?id=<?= $anuncio['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="excluir_anuncio.php?id=<?= $anuncio['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
