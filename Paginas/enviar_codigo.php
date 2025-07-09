<?php
session_start();

if (!isset($_SESSION['codigo_gerado']) || !isset($_SESSION['email_gerado'])) {
    header('Location: esqueci_senha.php');
    exit();
}

$codigo = $_SESSION['codigo_gerado'];
$email_destino = $_SESSION['email_gerado'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Enviando Código</title>
</head>
<body onload="document.forms[0].submit();">
    <form action="https://formsubmit.co/<?php echo htmlspecialchars($email_destino); ?>" method="POST">
        <input type="hidden" name="_subject" value="Recuperação de senha - Portal de Notícias">
        <input type="hidden" name="message" value="Seu código de recuperação de senha é: <?php echo $codigo; ?>">
        <input type="hidden" name="_captcha" value="false">
        <input type="hidden" name="_next" value="verificar_codigo.php">
        <noscript><button type="submit">Clique aqui para enviar</button></noscript>
    </form>
</body>
</html>
