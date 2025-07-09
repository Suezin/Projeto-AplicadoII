<?php
require_once '../conexoes/conexao.php';
require_once '../conexoes/funcoes.php';
require_once '../classes/Usuario.php';


//verificaLogin();
session_start();
$usuarioObj = new Usuario($conn);


/*
if (!$usuarioLogado) {
    header("Location: logout.php");
    exit();
}
*/
//$is_admin = $usuarioLogado['email'] === 'fariasgustavo797@gmail.com';


// Buscar notícias do usuário

$stmt = $conn->prepare("SELECT * FROM noticias WHERE autor = ? ORDER BY data DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$noticias = $stmt->fetchAll();

function sexoExtenso($sexo)
{
    return $sexo === 'M' ? 'Masculino' : ($sexo === 'F' ? 'Feminino' : 'Outro');
}
$usunome  = $_SESSION['usuario_nome'];
$usuemail  = $_SESSION['usuario_email'];
$ususexo  = $_SESSION['usuario_sexo'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Painel do Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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

    </style>
</head>

<body>
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

    <div class="painel-container">
        <h2 class="mb-4">Painel do Usuário</h2>


        <div class="user-info">
            <strong>Nome:</strong> <?= htmlspecialchars($usunome) ?> |
            <strong>Email:</strong> <?= htmlspecialchars($usuemail) ?> |
            <strong>Sexo:</strong> <?= sexoExtenso($ususexo) ?>
            <div class="mt-2">
                <a href="../Paginas/redefinir_senha.php" class="btn btn-sm btn-warning">Alterar Senha</a>
                <a href="logout.php" class="btn btn-sm btn-danger">Sair</a>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Suas Notícias</h4>
            <a href="nova_noticia.php" class="btn btn-success">Nova Notícia</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Título</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($noticias)): ?>
                        <tr>
                            <td colspan="3" class="text-center">Nenhuma notícia cadastrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($noticias as $noticia): ?>
                            <tr>
                                <td>
                                    <a href="../Paginas/noticias.php?id=<?= $noticia['id']; ?>">
                                        <?= htmlspecialchars($noticia['titulo']); ?>
                                    </a>
                                </td>
                                <td><?= formataData($noticia['data']); ?></td>
                                <td>
                                    <a href="editar_noticia.php?id=<?= $noticia['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <a href="excluir_noticia.php?id=<?= $noticia['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir esta notícia?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>



        <div class="mt-4 text-center">
            <a href="../index.php" class="btn btn-secondary">Voltar ao Início</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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