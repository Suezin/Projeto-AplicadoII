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

try {
    // Excluir imagem do servidor
    if ($noticia['imagem'] && file_exists($noticia['imagem'])) {
        unlink($noticia['imagem']);
    }

    // Excluir do banco de dados
    $stmt = $conn->prepare("DELETE FROM noticias WHERE id = ? AND autor = ?");
    $stmt->execute([$id, $_SESSION['usuario_id']]);

    header("Location: dashboard.php");
    exit();
} catch (PDOException $e) {
    die("Erro ao excluir a notícia: " . $e->getMessage());
}
?>
