<?php
session_start();
include_once 'config/config.php';
include_once 'classes/Usuario.php';
include_once 'classes/Noticias.php';
include_once 'classes/Categoria.php';

$usuario = new Usuario($banco);
$noticias = new Noticias($banco);
$categoria = new Categoria($banco);

$todas_noticias = $noticias->ler();

// Buscar as 5 últimas notícias
$ultimas_noticias = $banco->query("SELECT * FROM noticias ORDER BY data DESC, id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$erro_login = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entrar'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Buscar usuário pelo email
    $dados_usuario = $usuario->buscarPorEmail($email);

    if ($dados_usuario && password_verify($senha, $dados_usuario['senha'])) {
        $_SESSION['usuario_id'] = $dados_usuario['id'];
        header('Location: portal.php');
        exit();
    } else {
        $erro_login = 'Email ou senha inválidos';
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSL Times - Usuário</title>
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body class="portal-body">
    <!-- ====== CABEÇALHO ====== -->
    <div class="currency-ticker">
        <button class="hamburger" id="hamburger-currency" aria-label="Mostrar moedas" onclick="alternarMenuMoedas()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="ticker-content" id="ticker-content">
            <div class="ticker-items">
                <div class="ticker-item">
                    <i class="fas fa-dollar-sign"></i>
                    <span class="currency-name">USD</span>
                    <span class="currency-value" id="usd-value">5,56</span>
                </div>
                <div class="ticker-item">
                    <i class="fas fa-euro-sign"></i>
                    <span class="currency-name">EUR</span>
                    <span class="currency-value" id="eur-value">6,48</span>
                </div>
                <div class="ticker-item">
                    <i class="fab fa-bitcoin"></i>
                    <span class="currency-name">BTC</span>
                    <span class="currency-value" id="btc-value">597.466,26</span>
                </div>
                <div class="ticker-item">
                    <i class="fas fa-pound-sign"></i>
                    <span class="currency-name">GBP</span>
                    <span class="currency-value" id="gbp-value">7,59</span>
                </div>
            </div>
           <div class="index-nav">
                <a href="portal.php" class="login-btn">Meu Portal</a>
                <a href="logout.php" class="logout-btn"> Sair</a>
            </div>
        </div>
    </div>
    <style>
    .hamburger {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        margin-right: 6px;
        cursor: pointer;
    }
    @media (max-width: 768px) {
        .currency-ticker {
            display: flex;
            align-items: center;
        }
        .hamburger {
            display: block;
        }
        .ticker-content {
            display: none;
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            background: #222;
            border-radius: 8px;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            padding: 6px 8px 8px 8px;
            min-width: 150px;
            max-width: 90vw;
            text-align: center;
        }
        .ticker-content.active {
            display: block;
        }
        .ticker-items {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            margin-bottom: 8px;
        }
        .ticker-item {
            font-size: 0.85rem;
            width: 100%;
        }
        .login-btn {
            width: 100%;
            margin: 0 auto;
            margin-top: 2px;
            border-radius: 6px;
            font-size: 0.9rem;
            padding: 4px 0;
        }
    }
    </style>
    <script>
    function alternarMenuMoedas() {
        var ticker = document.getElementById('ticker-content');
        ticker.classList.toggle('active');
    }
    // Fecha o menu se clicar fora
    document.addEventListener('click', function(event) {
        var ticker = document.getElementById('ticker-content');
        var hamburger = document.getElementById('hamburger-currency');
        if (window.innerWidth <= 768) {
            if (!ticker.contains(event.target) && event.target !== hamburger) {
                ticker.classList.remove('active');
            }
        }
    });
    </script>

    <header class="main-header">
        <div class="header-content">
          
        </div>
    </header>

    <!-- ====== LISTA DE NOTÍCIAS EM DESTAQUE ====== -->
    <main class="news-container">
        <section class="featured-news" style="text-align:center;">
            <img src="./assets/img/logo2.png" alt="Logo CSL Times" class="logo-img" style="display:block;margin:0 auto 10px auto;max-width:250px;">
            <h2>CSL Times - Your window to the world!</h2>
            <?php if (empty($todas_noticias)): ?>
                <div class="empty-state">
                    <p>Publique a sua notícia, acessando o portal!</p>
                </div>
            <?php else: ?>
                <div class="news-grid">
                    <?php foreach ($todas_noticias as $noticia): ?>
                        <article class="news-card" onclick="abrirModalNoticia(<?php echo htmlspecialchars(json_encode($noticia)); ?>, '<?php echo htmlspecialchars($usuario->lerPorId($noticia['autor'])['nome'] ?? 'Autor desconhecido'); ?>', '<?php echo htmlspecialchars($categoria->lerPorId($noticia['categoria'])['nome'] ?? 'Sem categoria'); ?>')">
                            <?php if (!empty($noticia['imagem'])): ?>
                                <div class="news-image">
                                    <?php
                                    $img = ltrim($noticia['imagem'], '@');
                                    if (strpos($img, 'http') === 0) {
                                        $src = $img;
                                    } else {
                                        $src = 'uploads/' . $img;
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="news-content">
                                <h3 class="news-title"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                <p class="news-excerpt"><?php echo htmlspecialchars(substr($noticia['noticia'], 0, 150)) . '...'; ?></p>
                                <div class="news-meta">
                                    <div class="news-meta-top">
                                        <span class="news-author">
                                            <i class="fas fa-user"></i>
                                            <?php 
                                                $autor = $usuario->lerPorId($noticia['autor']);
                                                echo htmlspecialchars($autor['nome'] ?? 'Autor desconhecido');
                                            ?>
                                        </span>
                                        <span class="news-date">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($noticia['data'])); ?>
                                        </span>
                                    </div>
                                    <span class="news-category">
                                        <i class="fas fa-tag"></i>
                                        <?php 
                                            $cat = $categoria->lerPorId($noticia['categoria']);
                                            echo htmlspecialchars($cat['nome'] ?? 'Sem categoria');
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <div class="modal" id="loginModal">
        <div class="modal-content">
            <span class="close-modal" onclick="fecharModal()">&times;</span>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required placeholder="Seu email">
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" required placeholder="Sua senha">
                </div>
                <button type="submit" name="entrar" class="submit-btn">Entrar</button>
            </form>
            <div class="register-link">
                <p style="color: black;">Não tem uma conta? <a href="./registrar.php">Registre-se aqui</a></p>
            </div>
            
            <?php if (!empty($erro_login)): ?>
                <div class="login-error"><?php echo $erro_login; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal-noticia" id="noticiaModal">
        <div class="modal-noticia-content">
            <span class="close-modal-noticia" onclick="fecharModalNoticia()">&times;</span>
            <div class="modal-noticia-header">
                <h2 id="modal-noticia-titulo"></h2>
                <div class="modal-noticia-meta">
                    <div class="modal-noticia-date">
                        <i class="fas fa-calendar"></i>
                        <span id="modal-noticia-data"></span>
                    </div>
                    <div class="modal-noticia-author">
                        <i class="fas fa-user"></i>
                        <span id="modal-noticia-autor"></span>
                    </div>
                    <div class="modal-noticia-category">
                        <i class="fas fa-tag"></i>
                        <span id="modal-noticia-categoria"></span>
                    </div>
                </div>
            </div>
            <div class="modal-noticia-image" id="modal-noticia-imagem-container" style="display: none;">
                <img id="modal-noticia-imagem" src="" alt="">
            </div>
            <div class="modal-noticia-body">
                <p id="modal-noticia-conteudo"></p>
            </div>
        </div>
    </div>

    <footer class="footer-main" style="display: none;">
        <div class="social-links">
            <a href="https://br.linkedin.com" class="linkedin" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
            <a href="https://pt-br.facebook.com" class="facebook" title="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="https://www.instagram.com" class="instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://www.youtube.com/?gl=BR" class="youtube" title="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="https://x.com/" class="twitter" title="Twitter"><i class="fab fa-twitter"></i></a>
        </div>
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> CSL Times. Todos os direitos reservados.
        </div>
    </footer>

    <script>
        window.addEventListener('scroll', function() {
            const footer = document.querySelector('.footer-main');
            const scrollPosition = window.scrollY + window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            
            if (documentHeight - scrollPosition < 100) {
                footer.style.display = 'block';
            } else {
                footer.style.display = 'none';
            }
        });

        function abrirModal() {
            document.getElementById('loginModal').classList.add('active');
        }

        function fecharModal() {
            document.getElementById('loginModal').classList.remove('active');
        }

        window.onclick = function(event) {
            const loginModal = document.getElementById('loginModal');
            if (event.target == loginModal) {
                fecharModal();
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                fecharModal();
            }
        });

        function abrirModalNoticia(noticia, autor, categoria) {
            document.getElementById('modal-noticia-titulo').textContent = noticia.titulo;
            
            const data = new Date(noticia.data);
            const dataFormatada = data.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            document.getElementById('modal-noticia-data').textContent = dataFormatada;
            
            document.getElementById('modal-noticia-autor').textContent = autor;
            document.getElementById('modal-noticia-categoria').textContent = categoria;
            document.getElementById('modal-noticia-conteudo').textContent = noticia.noticia;
            
            // Configurar a imagem se existir
            const imagemContainer = document.getElementById('modal-noticia-imagem-container');
            const imagem = document.getElementById('modal-noticia-imagem');
            
            if (noticia.imagem && noticia.imagem.trim() !== '') {
                let imgSrc = noticia.imagem;
                if (imgSrc.startsWith('@')) {
                    imgSrc = imgSrc.substring(1);
                }
                if (!imgSrc.startsWith('http')) {
                    imgSrc = 'uploads/' + imgSrc;
                }
                imagem.src = imgSrc;
                imagem.alt = noticia.titulo;
                imagemContainer.style.display = 'flex';
            } else {
                imagemContainer.style.display = 'none';
            }
            
            document.getElementById('noticiaModal').style.display = 'flex';
        }

        function fecharModalNoticia() {
            document.getElementById('noticiaModal').style.display = 'none';
        }

        window.addEventListener('click', function(event) {
            const noticiaModal = document.getElementById('noticiaModal');
            if (event.target === noticiaModal) {
                fecharModalNoticia();
            }
        });

    </script>
</body>
</html>