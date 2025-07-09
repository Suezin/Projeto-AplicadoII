<?php
function verificaLogin() {
    if (!isset($_SESSION['usuario_id'])) {
      
        header("Location: ../posLogin/dashboard.php");
        exit();
    }
}

function sanitizaEntrada($entrada) {
    return htmlspecialchars(trim($entrada), ENT_QUOTES, 'UTF-8');
}

function formataData($data) {
    return date('d/m/Y H:i', strtotime($data));
}

function resumoTexto($texto, $limite = 150) {
    $texto = strip_tags($texto);
    return (strlen($texto) <= $limite) ? $texto : substr($texto, 0, $limite) . '...';
}

function uploadImagem($arquivo) {
    $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    $tamanhoMax = 5 * 1024 * 1024; // 5MB

    if (!in_array($arquivo['type'], $permitidos)) {
        return false;
    }

    if ($arquivo['size'] > $tamanhoMax) {
        return false;
    }

    $ext = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
    $nomeArquivo = uniqid('img_') . '.' . $ext;
    $caminho = 'uploads/' . $nomeArquivo;

    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
        return $caminho;
    }

    return false;
}
?>
