<?php
session_start();
require_once './conexoes/conexao.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Verifica se o ID foi passado corretamente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID inválido.";
    exit;
}

$id = (int)$_GET['id'];

// Exclui o anúncio do banco
$stmt = $conn->prepare("DELETE FROM anuncio WHERE id = ?");
$stmt->execute([$id]);

// Redireciona para a listagem
header("Location: listar_anuncios.php");
exit;
?>
