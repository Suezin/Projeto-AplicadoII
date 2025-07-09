<?php
// deletar.php
require_once './conexoes/conexao.php';
require_once './classes/Usuario.php'; // ou o caminho do seu arquivo de classe

$usuario = new Usuario($conn); // ou da forma como você instancia
$id = $_GET['id'] ?? null;

if ($id && $usuario->deletar($id)) {
    header('Location: portal.php'); // redireciona após deletar
    exit;
} else {
    echo "Erro ao deletar usuário.";
}
