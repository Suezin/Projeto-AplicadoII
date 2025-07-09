<?php
session_start();

require_once './conexoes/conexao.php';
require_once './conexoes/funcoes.php';
require_once './classes/Usuario.php';

$usuario = new Usuario($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['senha']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($dados_usuario = $usuario->login($email, $senha)) {
        $_SESSION['usuario_id'] = $dados_usuario['id'];
        header('Location: ./posLogin/dashboard.php');
        exit();
    } else {
        $mensagem_erro = "Credenciais inválidas!";
    }
}

$stmt = $conn->query("SELECT n.*, u.nome AS nome_autor 
                      FROM noticias n 
                      JOIN usuarios u ON n.autor = u.id 
                      WHERE u.ativo = 1
                      ORDER BY n.data DESC");
$noticias = $stmt->fetchAll();

$tituloPesquisado = $_GET['busca'] ?? '';
$noticiasFiltradas = [];

if (!empty($tituloPesquisado)) {
    foreach ($noticias as $noticia) {
        if (stripos($noticia['titulo'], $tituloPesquisado) !== false) {
            $noticiasFiltradas[] = $noticia;
        }
    }
} else {
    $noticiasFiltradas = $noticias;
}


$stmt = $conn->query("SELECT * FROM anuncio  
                      WHERE ativo = 1 AND destaque = 0
                      ORDER BY data_cadastro DESC");
$anuncios = $stmt->fetchAll();
//var_dump($anuncios);
//exit();

$stmt = $conn->query("SELECT * FROM anuncio
                    WHERE ativo = 1 AND
                    destaque = 1;");
$anuncio_destaque = $stmt->fetchAll();

?>



<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Portal de Notícias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">

</head>



<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand ms-auto" href="index.php">Portal de Notícias</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="./posLogin/nova_noticia.php">Nova Notícia</a></li>
                        <li class="nav-item"><a class="nav-link" href="./cadastrar_anuncio.php">Anucie aqui</a></li>
                        <li class="nav-item"><a class="nav-link" href="./portal.php">Usuários</a></li>
                        <li class="nav-item"><a class="nav-link" href="./posLogin/dashboard.php">Meu Painel</a></li>
                        <li class="nav-item"><a class="nav-link" href="./posLogin/logout.php">Sair</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="./Paginas/login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="./Paginas/cadastro.php">Cadastro</a></li>
                    <?php endif; ?>
                </ul>

                <!-- Botão de tema -->
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
        </div>
    </nav>

    <div class="anuncio-destaque d-flex justify-content-center">
        <a href="" id="link-destaque"><img id="img-destaque" src="" class="img-destaque img-fluid" style="max-width:55em; max-height:20em"></a>
    </div>
    <div id="popup-anuncio" style="position: fixed; bottom: 10px; right: 10px; width: 320px; background: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.3); overflow: hidden; z-index: 9999;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px;">
            <h6><strong class="titulo-de-anuncio">Anúncio</strong></h6>
            <span class="btn-fechar-anuncio" onclick="fecharAnuncio()">&times;</span>
        </div>
        <div class="div-image">
            <img id="img-anuncio" src="" class="img-anuncio" style="max-width: 20em; max-height:20em">
        </div>
        <div style="padding: 12px;" class="info-anuncio">
            <h6 id="titulo-anuncio" style="font-weight: bold;" class="titulo_anuncio"></h6>
            <p id="resumo-anuncio" style="font-size: 0.9rem;" class="resumo_anuncio"></p>
        </div>
        <div class="btn-anuncio">
            <a id="link-anuncio" class="btn btn-primary btn-sm">Saiba mais</a>
        </div>
    </div>

    <script>
        const anuncios = <?= json_encode($anuncios) ?>;
        const destaque = <?= json_encode($anuncio_destaque) ?>;

        let indiceAtual = 0;

        function fecharAnuncio() {
            document.getElementById('popup-anuncio').style.display = 'none';
        }

        function atualizarAnuncio() {
            if (anuncios.length === 0) {
                document.getElementById('popup-anuncio').style.display = 'none';
                return;
            }

            const anuncio = anuncios[indiceAtual];
            document.getElementById('img-anuncio').src = anuncio.imagem;
            document.getElementById('titulo-anuncio').innerText = anuncio.nome;
            document.getElementById('resumo-anuncio').innerText = anuncio.texto;
            document.getElementById('link-anuncio').href = anuncio.link;

            document.getElementById('popup-anuncio').style.display = 'block';

            indiceAtual = (indiceAtual + 1) % anuncios.length;
        }

        // Chamada inicial e intervalo de rotação
        atualizarAnuncio();
        setInterval(atualizarAnuncio, 60000); // a cada 60s

        let indiceDestaque = 0;

        function atualizarAnuncioDestaque() {
            if (destaque.length === 0) {
                document.querySelector('.anuncio-destaque').style.display = 'none';
                return;
            }

            const anuncio = destaque[indiceDestaque];
            document.getElementById('img-destaque').src = anuncio.imagem;
            document.getElementById('link-destaque').href = anuncio.link;

            indiceDestaque = (indiceDestaque + 1) % destaque.length;
        }

        // Chamada inicial e intervalo de rotação
        atualizarAnuncioDestaque();
        setInterval(atualizarAnuncioDestaque, 60000); // a cada 60s
    </script>


    <div class="container-mt">
        <div id="weather-box" class="weather-aside">
            <div class="card-clima">
                <div class="temp-container">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-thermometer" viewBox="0 0 24 24"
                        fill="currentColor" width="32" height="32" style="margin-right:8px;">
                        <path d="M14 14.76V5a2 2 0 10-4 0v9.76a4 4 0 104 0z" />
                    </svg>
                    <p class="main-text" id="temp-clima">--°</p>
                </div>
                <div class="info">
                    <p class="info-" id="cidade-clima">--</p><br>
                    <p class="info-right" id="desc-clima">--</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <h1 class="mb-4">Últimas Notícias</h1>

        <div id="poda">
            <!-- Efeitos visuais de fundo -->
            <div class="glow"></div>
            <div class="white"></div>
            <div class="border"></div>
            <div class="darkBorderBg"></div>

            <!-- Campo principal -->
            <div id="main">
                <form method="GET">
                    <input
                        class="input"
                        type="text"
                        name="busca"
                        placeholder="Buscar notícia"
                        value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" />
                    <div id="input-mask"></div>
                    <div id="pink-mask"></div>
                    <button type="submit" id="filter-icon">
                        🔍
                    </button>
                </form>
                <div class="filterBorder"></div>

            </div>
        </div>

        <?php if (!empty($mensagem_erro)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
        <?php endif; ?>

        <div class="row">
            <?php if (count($noticiasFiltradas) === 0): ?>
                <p class="text-muted">Nenhuma notícia encontrada para "<?= htmlspecialchars($tituloPesquisado) ?>"</p>
            <?php else: ?>
                <?php foreach ($noticiasFiltradas as $noticia): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($noticia['imagem'])): ?>
                                <img src="<?= htmlspecialchars($noticia['imagem']) ?>" class="card-img-top" alt="Imagem" />
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($noticia['titulo']) ?></h5>
                                <p class="card-text"><?= resumoTexto($noticia['noticia']) ?></p>
                                <small class="text-muted">Por <?= htmlspecialchars($noticia['nome_autor']) ?> em <?= formataData($noticia['data']) ?></small>
                                <div class="mt-2">
                                    <a href="Paginas/noticias.php?id=<?= (int)$noticia['id'] ?>" class="btn btn-primary btn-sm">Ler mais</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
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
    <script>
        const API_KEY = '980626a885a09b461bbf3d8b1b600ef3'; // sua chave real aqui

        function mostrarErro(msg) {
            document.getElementById("temp-clima").innerText = '--°';
            document.getElementById("cidade-clima").innerText = msg;
            document.getElementById("desc-clima").innerText = '---';
            document.getElementById("temp-range").innerText = '';
        }

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                success => {
                    const lat = success.coords.latitude;
                    const lon = success.coords.longitude;

                    const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${API_KEY}&units=metric&lang=pt_br`;
                    console.log('Buscando clima em:', lat, lon);
                    fetch(url)
                        .then(res => {
                            if (!res.ok) throw new Error(`Erro da API: ${res.status} ${res.statusText}`);
                            return res.json();
                        })
                        .then(data => {
                            console.log('Dados do clima:', data);
                            const temp = Math.round(data.main.temp);
                            const cidade = data.name;
                            const descricao = data.weather[0].description;

                            document.getElementById("temp-clima").innerText = `${temp}°`;
                            document.getElementById("cidade-clima").innerText = cidade;
                            document.getElementById("desc-clima").innerText = descricao;
                        })
                        .catch(err => {
                            console.error('Erro ao buscar clima:', err);
                            mostrarErro('Não foi possível obter dados do clima');
                        });
                },
                error => {
                    console.error('Erro de geolocalização:', error);
                    mostrarErro('Erro ao obter sua localização');
                }
            );
        } else {
            mostrarErro('Geolocalização não suportada');
        }
    </script>
</body>

</html>