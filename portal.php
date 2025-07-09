<?php
session_start();
require_once './conexoes/conexao.php';
require_once './classes/Usuario.php';
require_once './conexoes/funcoes.php';



$usuario = new Usuario($conn);

// Apenas usuários ativos devem aparecer
$search = $_GET['search'] ?? '';
$order_by = $_GET['order_by'] ?? '';
$usuarios = $usuario->ler($search, $order_by);

// Informações do usuário logado
$dados_usuario = $usuario->lerPorId($_SESSION['usuario_id']);
$nome_usuario = $dados_usuario['nome'] ?? 'Desconhecido';

function saudacao()
{
    $hora = date('H');
    if ($hora >= 6 && $hora < 12) return "Bom dia";
    elseif ($hora >= 12 && $hora < 18) return "Boa tarde";
    else return "Boa noite";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel de Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            color: #0f2027;
        }

        nav a {
            margin-right: 15px;
            color: #0072ff;
            text-decoration: none;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .btn-danger {
            margin-left: 5px;
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
        .colocarnocanto{
            width: 100%;
            display: flex;
            justify-content: end;
        }
        body.dark-mode {
    background-color: #121212;
    color: #f1f1f1;
}

body.dark-mode .container {
    background-color: #1e1e1e;
    color: #f1f1f1;
}

body.dark-mode table {
    background-color: #1a1a1a;
    color: #ddd;
}

body.dark-mode .table-dark th {
    background-color: #333 !important;
}

body.dark-mode .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

body.dark-mode .btn-danger {
    background-color: #dc3545;
}

body.dark-mode input,
body.dark-mode select {
  
    border-color: #444;
}
body.dark-mode h1 {
    color: white ;
}
body.dark-mode h2 {
    color: white ;
}

    </style>
</head>

<body>
    <div class="container">
        <header class="mb-4">
            <div class="colocarnocanto">
                <form class="d-flex align-items-center ms-3" onsubmit="return false;">
                    <label class="theme-switch">
                        <input type="checkbox" class="theme-switch__checkbox" id="themeToggle" />
                        <div class="theme-switch__container">
                            <div class="theme-switch__clouds"></div>
                            <div class="theme-switch__stars-container">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 144 55" fill="none">
                                    <path fill="currentColor" d="M135.831 3.00688C135.055...Z" />
                                </svg>
                            </div>
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
                </form>
            </div>
            <h2><?= saudacao() . ', ' . htmlspecialchars($nome_usuario) ?>!</h2>

            <nav>
                <a href="index.php">Início</a>
                <a href="posLogin/dashboard.php">Painel</a>
                <a href="posLogin/logout.php">Sair</a>
            </nav>
        </header>

        <h1>Lista de Usuários</h1>

        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Buscar por nome ou e-mail" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="order_by">
                    <option value="">Ordenação</option>
                    <option value="nome" <?= $order_by === 'nome' ? 'selected' : '' ?>>Nome</option>
                    <option value="sexo" <?= $order_by === 'sexo' ? 'selected' : '' ?>>Sexo</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>

        <table class="table table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Sexo</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nome']) ?></td>
                        <td><?= $row['sexo'] === 'M' ? 'Masculino' : ($row['sexo'] === 'F' ? 'Feminino' : 'Outro') ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="editarUsu.php?id=<?= $row['id'] ?>">Editar</a>
                            <a class="btn btn-sm btn-danger" href="deletar.php?id=<?= $row['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir?')">
                                Excluir
                            </a>


                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
            const themeToggle = document.getElementById("themeToggle");
            const savedTheme = localStorage.getItem("theme");

            if (savedTheme === "dark") {
                document.body.classList.add("dark-mode");
                themeToggle.checked = true;
            }

            themeToggle.addEventListener("change", () => {
                const isDark = themeToggle.checked;
                document.body.classList.toggle("dark-mode", isDark);
                localStorage.setItem("theme", isDark ? "dark" : "light");
            });
        </script>
</body>



</html>