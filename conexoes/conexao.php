<?php
require_once __DIR__ . '/../classes/Database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Erro ao conectar ao banco de dados.");
}
?>
