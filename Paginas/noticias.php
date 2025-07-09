<?php
session_start();
require_once '../conexoes/conexao.php';
require_once '../conexoes/funcoes.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: ../index.php");
    exit();
}

$id = (int) $id;

$stmt = $conn->prepare("SELECT n.*, u.nome AS nome_autor 
                        FROM noticias n 
                        JOIN usuarios u ON n.autor = u.id 
                        WHERE n.id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(to right, #00c6ff, #0072ff);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }

        h1 {
            color: #00c6ff;
            text-shadow: 1px 1px 2px rgba(0, 198, 255, 0.5);
            border-left: 5px solid #00c6ff;
            padding-left: 10px;
        }

        p.text-muted {
            color: #a1b4c6 !important;
            font-size: 0.9rem;
        }

        .img-fluid {
            border-radius: 8px;
            border: 2px solid #00c6ff;
            box-shadow: 0 0 15px rgba(0, 198, 255, 0.3);
        }

        .mb-4 p {
            background-color: #1b1f23;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 198, 255, 0.15);
            line-height: 1.6;
            color: #dceaf5;
        }

        .btn-primary {
            background: linear-gradient(90deg, #00c6ff, #0072ff);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #0072ff, #00c6ff);
        }

        .btn-danger {
            background: #ff4b5c;
            border: none;
        }

        .btn-secondary {
            background-color: #2c5364;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #203a43;
        }

        .container {
            margin-top: 30px;
        }

        @media (max-width: 992px) {
            h1 {
                font-size: 1.8rem;
                padding-left: 8px;
            }

            .text-muted {
                font-size: 0.85rem;
            }

            .btn {
                font-size: 0.95rem;
                padding: 0.4rem 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .card-img-top,
            .img-fluid {
                width: 100%;
                height: auto;
                max-height: 280px;
                object-fit: cover;
                margin-bottom: 1rem;
            }

            .mb-4 p {
                font-size: 1rem;
                padding: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            h1 {
                font-size: 1.5rem;
            }

            .btn {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

            .mb-3,
            .mb-4 {
                margin-bottom: 1rem !important;
            }
        }

        :root {
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

        body {
            background: #f0f2f5;
        }

        .painel-container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .user-info {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
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

        body.dark-mode {
            background-color: #121212;
            color: #f1f1f1;
        }

        body.dark-mode .painel-container {
            background-color: #1e1e1e;
            color: #f1f1f1;
        }

        body.dark-mode .user-info {
            background-color: #2c2c2c;
            color: #ffffff;
        }

        body.dark-mode .table {
            background-color: #2a2a2a;
            color: #f1f1f1;
        }

        body.dark-mode .table-dark thead {
            background-color: #333;
            color: #fff;
        }

        body.dark-mode .btn {
            border: none;
        }

        body.dark-mode .btn-success {
            background-color: #28a745;
        }

        body.dark-mode .btn-primary {
            background-color: #007bff;
        }

        body.dark-mode .btn-danger {
            background-color: #dc3545;
        }

        body.dark-mode .btn-warning {
            background-color: #ffc107;
        }

        body.dark-mode .btn-secondary {
            background-color: #6c757d;
        }

        body.dark-mode a {
            color: #f1f1f1;
        }
        .neon-info {
    color:rgb(60, 80, 80);
    text-shadow: 0 0 5pxrgb(32, 44, 44), 0 0 10pxrgb(255, 255, 255), 0 0 20pxrgb(28, 31, 31);
    font-weight: bold;
}
    </style>
</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Portal de Notícias</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="../posLogin/dashboard.php">Painel</a></li>
                        <li class="nav-item"><a class="nav-link" href="../posLogin/logout.php">Sair</a></li>
                        <div class="d-flex justify-content-end p-3">
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
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="cadastro.php">Cadastro</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-3" id="tituloNoticia"><?= htmlspecialchars($noticia['titulo']) ?></h1>
        <p class="text-muted neon-info">Por <?= htmlspecialchars($noticia['nome_autor']) ?> em <?= formataData($noticia['data']) ?></p>

        <?php if (!empty($noticia['imagem'])): ?>
            <img src="../<?= htmlspecialchars($noticia['imagem']) ?>" class="card-img-top" id="imagemNoticia" alt="Imagem">
        <?php endif; ?>

        <div class="mb-4">
            <p id="conteudoNoticia"><?= nl2br(htmlspecialchars($noticia['noticia'])) ?></p>
        </div>

        <button id="exportarPdfBtn" class="btn btn-outline-primary mb-3">Exportar em PDF</button>

        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $noticia['autor']): ?>
            <div class="mb-3">
                <a href="../posLogin/editar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-primary">Editar</a>
                <a href="../posLogin/excluir_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-danger" onclick="return confirm('Deseja realmente excluir esta notícia?')">Excluir</a>
            </div>
        <?php endif; ?>

        <a href="../index.php" class="btn btn-secondary">Voltar</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("exportarPdfBtn").addEventListener("click", () => {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF("p", "mm", "a4");
            const margin = 10;
            let y = margin;

            const titulo = document.getElementById("tituloNoticia").innerText;
            const autorData = document.getElementById("autorData").innerText;
            const conteudoHtml = document.getElementById("conteudoNoticia").innerHTML;
            const conteudo = conteudoHtml
                .replace(/<br\s*\/?>/gi, "\n")
                .replace(/<[^>]+>/g, '')
                .replace(/\s+/g, ' ')
                .trim();
            const imagemEl = document.getElementById("imagemNoticia");

            doc.setFont("Helvetica", "bold");
            doc.setFontSize(18);
            doc.setTextColor(0, 198, 255);
            doc.text(titulo, margin, y);
            y += 10;

            doc.setFont("Helvetica", "normal");
            doc.setFontSize(12);
            doc.setTextColor(100);
            doc.text(autorData, margin, y);
            y += 10;

            if (imagemEl) {
                const img = new Image();
                img.crossOrigin = "anonymous";
                img.src = imagemEl.src;

                img.onload = () => {
                    const imgWidth = 180;
                    const imgHeight = (img.height / img.width) * imgWidth;
                    doc.addImage(img, "JPEG", margin, y, imgWidth, imgHeight);
                    y += imgHeight + 10;

                    doc.setFont("times", "normal");
                    doc.setFontSize(12);
                    doc.setTextColor(33, 33, 33);

                    const linhas = doc.splitTextToSize(conteudo, 180);
                    doc.text(linhas, margin, y, {
                        maxWidth: 180,
                        lineHeightFactor: 1.4
                    });
                    doc.save("noticia.pdf");
                };
            } else {
                doc.setFont("times", "normal");
                doc.setFontSize(12);
                doc.setTextColor(33, 33, 33);
                const linhas = doc.splitTextToSize(conteudo, 180);
                doc.text(linhas, margin, y, {
                    maxWidth: 180,
                    lineHeightFactor: 1.4
                });
                doc.save("noticia.pdf");
            }
        });
    </script>
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