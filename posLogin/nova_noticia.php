<?php
require_once '../conexoes/conexao.php';
require_once '../conexoes/funcoes.php';

session_start();

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = sanitizaEntrada($_POST['titulo'] ?? '');
    $noticia = sanitizaEntrada($_POST['noticia'] ?? '');
    $imagem = $_FILES['imagem'] ?? null;

    if (empty($titulo) || empty($noticia)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        $imagem_path = null;

        if ($imagem && $imagem['error'] === 0) {
            $pasta_destino = '../uploads/';
            if (!is_dir($pasta_destino)) {
                mkdir($pasta_destino, 0777, true);
            }

            $nome_arquivo = uniqid() . '_' . basename($imagem['name']);
            $caminho_arquivo = $pasta_destino . $nome_arquivo;

            if (move_uploaded_file($imagem['tmp_name'], $caminho_arquivo)) {
                $imagem_path = 'uploads/' . $nome_arquivo;
            } else {
                $erro = "Erro ao fazer upload da imagem.";
            }
        }

        if (empty($erro)) {
            $stmt = $conn->prepare("INSERT INTO noticias (titulo, noticia, autor, imagem) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$titulo, $noticia, $_SESSION['usuario_id'], $imagem_path])) {
                $sucesso = "Notícia publicada com sucesso!";
                $titulo = $noticia = '';
            } else {
                $erro = "Erro ao salvar no banco de dados.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Notícia - Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            transition: background-color 0.5s ease, color 0.5s ease;
        }
        body.dark-mode {
            background-color: #121212;
            color: #f1f1f1;
        }

        /* === Botão estilizado modo claro/escuro === */
        .theme-switch {
            --toggle-size: 10px;
            --container-width: 5.625em;
            --container-height: 2.5em;
            --container-radius: 6.25em;
            --container-light-bg: #3D7EAE;
            --container-night-bg: #1D1F2C;
            --circle-container-diameter: 3.375em;
            --sun-moon-diameter: 2.125em;
            --sun-bg: #ECCA2F;
            --moon-bg: #C4C9D1;
            --spot-color: #959DB1;
            --circle-container-offset: calc((var(--circle-container-diameter) - var(--container-height)) / 2 * -1);
            --stars-color: #fff;
            --clouds-color: #F3FDFF;
            --back-clouds-color: #AACADF;
            --transition: .5s cubic-bezier(0, -0.02, 0.4, 1.25);
            --circle-transition: .3s cubic-bezier(0, -0.02, 0.35, 1.17);
        }

        .theme-switch,
        .theme-switch *,
        .theme-switch *::before,
        .theme-switch *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-size: var(--toggle-size);
        }

        .theme-switch__container {
            width: var(--container-width);
            height: var(--container-height);
            background-color: var(--container-light-bg);
            border-radius: var(--container-radius);
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0em -0.062em 0.062em rgba(0, 0, 0, 0.25),
                0em 0.062em 0.125em rgba(255, 255, 255, 0.94);
            transition: var(--transition);
            position: relative;
        }

        .theme-switch__container::before {
            content: "";
            position: absolute;
            z-index: 1;
            inset: 0;
            box-shadow: 0em 0.05em 0.187em rgba(0, 0, 0, 0.25) inset,
                0em 0.05em 0.187em rgba(0, 0, 0, 0.25) inset;
            border-radius: var(--container-radius);
        }

        .theme-switch__checkbox {
            display: none;
        }

        .theme-switch__circle-container {
            width: var(--circle-container-diameter);
            height: var(--circle-container-diameter);
            background-color: rgba(255, 255, 255, 0.1);
            position: absolute;
            left: var(--circle-container-offset);
            top: var(--circle-container-offset);
            border-radius: var(--container-radius);
            box-shadow: inset 0 0 0 3.375em rgba(255, 255, 255, 0.1),
                0 0 0 0.625em rgba(255, 255, 255, 0.1),
                0 0 0 1.25em rgba(255, 255, 255, 0.1);
            display: flex;
            transition: var(--circle-transition);
            pointer-events: none;
        }

        .theme-switch__sun-moon-container {
            pointer-events: auto;
            position: relative;
            z-index: 2;
            width: var(--sun-moon-diameter);
            height: var(--sun-moon-diameter);
            margin: auto;
            border-radius: var(--container-radius);
            background-color: var(--sun-bg);
            box-shadow: inset 0.062em 0.062em 0.062em rgba(254, 255, 239, 0.61),
                inset 0em -0.062em 0.062em #a1872a;
            filter: drop-shadow(0.062em 0.125em 0.125em rgba(0, 0, 0, 0.25)) drop-shadow(0em 0.062em 0.125em rgba(0, 0, 0, 0.25));
            overflow: hidden;
            transition: var(--transition);
        }

        .theme-switch__moon {
            transform: translateX(100%);
            width: 100%;
            height: 100%;
            background-color: var(--moon-bg);
            border-radius: inherit;
            box-shadow: inset 0.062em 0.062em 0.062em rgba(254, 255, 239, 0.61),
                inset 0em -0.062em 0.062em #969696;
            transition: var(--transition);
            position: relative;
        }

        .theme-switch__spot {
            position: absolute;
            top: 0.75em;
            left: 0.312em;
            width: 0.75em;
            height: 0.75em;
            border-radius: var(--container-radius);
            background-color: var(--spot-color);
            box-shadow: inset 0em 0.0312em 0.062em rgba(0, 0, 0, 0.25);
        }

        .theme-switch__spot:nth-of-type(2) {
            width: 0.375em;
            height: 0.375em;
            top: 0.937em;
            left: 1.375em;
        }

        .theme-switch__spot:nth-last-of-type(3) {
            width: 0.25em;
            height: 0.25em;
            top: 0.312em;
            left: 0.812em;
        }

        .theme-switch__clouds {
            width: 1.25em;
            height: 1.25em;
            background-color: var(--clouds-color);
            border-radius: var(--container-radius);
            position: absolute;
            bottom: -0.625em;
            left: 0.312em;
            box-shadow: 0.937em 0.312em var(--clouds-color),
                -0.312em -0.312em var(--back-clouds-color),
                1.437em 0.375em var(--clouds-color),
                0.5em -0.125em var(--back-clouds-color),
                2.187em 0 var(--clouds-color),
                1.25em -0.062em var(--back-clouds-color),
                2.937em 0.312em var(--clouds-color),
                2em -0.312em var(--back-clouds-color);
            transition: var(--transition);
        }

        .theme-switch__stars-container {
            position: absolute;
            color: var(--stars-color);
            top: -100%;
            left: 0.312em;
            width: 2.75em;
            transition: var(--transition);
        }

        /* Animações quando ativado */
        .theme-switch__checkbox:checked+.theme-switch__container {
            background-color: var(--container-night-bg);
        }

        .theme-switch__checkbox:checked+.theme-switch__container .theme-switch__circle-container {
            left: calc(100% - var(--circle-container-offset) - var(--circle-container-diameter));
        }

        .theme-switch__checkbox:checked+.theme-switch__container .theme-switch__moon {
            transform: translateX(0);
        }

        .theme-switch__checkbox:checked+.theme-switch__container .theme-switch__clouds {
            bottom: -4.062em;
        }

        .theme-switch__checkbox:checked+.theme-switch__container .theme-switch__stars-container {
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="../index.php">Portal de Notícias</a>
        <div class="d-flex align-items-center">
            <ul class="navbar-nav me-3">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Painel</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
            </ul>
            <label class="theme-switch">
                <input type="checkbox" class="theme-switch__checkbox" id="themeToggle">
                <div class="theme-switch__container">
                    <div class="theme-switch__clouds"></div>
                    <div class="theme-switch__stars-container">✨</div>
                    <div class="theme-switch__circle-container">
                        <div class="theme-switch__sun-moon-container">
                            <div class="theme-switch__moon">
                                <div class="theme-switch__spot"></div>
                                <div class="theme-switch__spot"></div>
                                <div class="theme-switch__spot"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4 text-center">Nova Notícia</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php elseif ($sucesso): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required value="<?= htmlspecialchars($titulo ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="noticia" class="form-label">Conteúdo</label>
            <textarea name="noticia" id="noticia" class="form-control" rows="8" required><?= htmlspecialchars($noticia ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem (opcional)</label>
            <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Publicar</button>
        <a href="../index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    const themeToggle = document.getElementById('themeToggle');
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeToggle.checked = true;
    }

    themeToggle.addEventListener('change', () => {
        const isDark = themeToggle.checked;
        document.body.classList.toggle('dark-mode', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
</script>
</body>
</html>
