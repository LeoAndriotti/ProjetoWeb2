<?php
include_once 'components/previsao_tempo.php';
// Inicia a sessão para controle de login
session_start();
// Inclui arquivos de configuração e classes principais
include_once 'config/config.php'; // Configurações do banco de dados
include_once 'classes/Usuario.php'; // Classe de usuário
include_once 'classes/Noticias.php'; // Classe de notícias
include_once 'classes/Categoria.php'; // Classe de categorias
include_once 'classes/Anuncio.php'; // Classe de anúncios
// Define constante para identificar que estamos na página inicial (index)
define('INDEX_MODE', true);
// Inclui o componente de card de notícia reutilizável
include_once 'components/noticiaCard.php';

// Instancia os objetos principais
$usuario = new Usuario($banco);
$noticias = new Noticias($banco);
$categoria = new Categoria($banco);
$anuncio = new Anuncio($banco);

// Busca todas as notícias do banco
$todas_noticias = $noticias->ler();
if (!is_array($todas_noticias)) {
    $todas_noticias = [];
}
// Busca as últimas 5 notícias para destaque
$ultimas_noticias = $banco->query("SELECT * FROM noticias ORDER BY data DESC, id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Buscar autores e categorias para o filtro
$autores = $usuario->ler();
$categorias = $categoria->lerTodas();

// Lógica de filtro
$filtro_titulo = isset($_GET['titulo']) ? trim($_GET['titulo']) : '';
$filtro_autor = isset($_GET['autor']) ? $_GET['autor'] : '';
$filtro_categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

$query = "SELECT * FROM noticias WHERE 1=1";
$params = [];
if ($filtro_titulo !== '') {
    $query .= " AND titulo LIKE ?";
    $params[] = "%$filtro_titulo%";
}
if ($filtro_autor !== '') {
    $query .= " AND autor = ?";
    $params[] = $filtro_autor;
}
if ($filtro_categoria !== '') {
    $query .= " AND categoria = ?";
    $params[] = $filtro_categoria;
}
$query .= " ORDER BY data DESC, id DESC";
$stmt = $banco->prepare($query);
$stmt->execute($params);
$todas_noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lógica de login (caso o formulário seja enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entrar'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $dados_usuario = $usuario->buscarPorEmail($email);
    if ($dados_usuario && password_verify($senha, $dados_usuario['senha'])) {
        // Login bem-sucedido, redireciona para área do usuário
        $_SESSION['usuario_id'] = $dados_usuario['id'];
        header('Location: indexUsuario.php');
        exit();
    } else {
        // Login falhou, exibe mensagem de erro
        $erro_login = 'Email ou senha inválidos';
    }
}

$anuncios_ativos = $anuncio->lerAtivos();
$anuncios_destaque = $anuncio->lerDestaques();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSL Times</title>
    <!-- Estilos principais do site -->
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="./uploads/Index.css">
    <link rel="stylesheet" href="./uploads/previsao_tempo.css">
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>

<body class="portal-body">
    <button id="toggle-theme" class="theme-toggle-btn" title="Alternar tema">
      <i class="fa-solid fa-moon"></i>
    </button>
        
    <!-- Container flexível para previsão do tempo e moedas -->
    <div style="display: flex; justify-content: center; align-items: center; gap: 32px; margin: 20px 0;">
        
        <?php include './components/moedas.php'; ?>
       
    </div>

    <!-- Botão de login fixo no topo direito -->
    <a href="logar.php" class="login-btn" style="position: absolute; right: 30px; top: 8px; z-index: 1100; font-size: 1rem; padding: 0.6rem 1.2rem;">Entrar</a>
    
    <!-- Cabeçalho principal (pode ser expandido para navegação) -->
    <header class="main-header">
        <div class="header-content">
            <!-- Espaço reservado para conteúdo do cabeçalho -->
        </div>
    </header>
    <!-- Filtro de busca -->
        <form method="get" class="filtro-busca-form">
            <div>
                <label for="titulo"><i class="fa-solid fa-heading"></i> Título:</label>
                <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($filtro_titulo) ?>" placeholder="Buscar por título...">
            </div>
            <div>
                <label for="autor"><i class="fa-solid fa-user"></i> Autor:</label>
                <select name="autor" id="autor">
                    <option value="">Todos</option>
                    <?php foreach ($autores as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $filtro_autor == $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="categoria"><i class="fa-solid fa-list"></i> Categoria:</label>
                <select name="categoria" id="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $filtro_categoria == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="submit-btn" style="height: 40px;"><i class="fa-solid fa-filter"></i> Filtrar</button>
        </form>

    <main class="news-container">
        
        <!-- Seção de destaque com logo e slogan -->
        <section class="featured-news" style="text-align:center;">
            <img src="./assets/img/logo2.png" alt="Logo CSL Times" class="logo-img" style="display:block;margin:0 auto 10px auto;max-width:250px;">
            <h2>CSL Times - Your window to the world!</h2>
            <?php if (empty($todas_noticias)): ?>
                <!-- Mensagem caso não haja notícias cadastradas -->
                <div class="empty-state">
                    <p>Publique a sua notícia, acessando o portal!</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Seção das últimas notícias -->
        <section class="ultimas-noticias">
            <h2>Últimas Notícias</h2>
            <?php if (!empty($ultimas_noticias)): ?>
                <div class="news-grid">
                    <!-- Renderiza cada notícia usando o componente reutilizável -->
                    <?php foreach ($todas_noticias as $noticia): ?>
                        <?php echo renderNoticiaCard($noticia, $usuario, $categoria); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Rodapé fixo com redes sociais e direitos autorais -->
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

    <!-- Pop-up de anúncios em destaque -->
    <div id="popup-anuncio-destaque" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.75);z-index:9999;justify-content:center;align-items:center;">
      <div style="background:#fff;padding:48px 36px 36px 36px;border-radius:24px;box-shadow:0 12px 48px rgba(0,0,0,0.35);max-width:1200px;max-height:90vh;display:flex;flex-direction:column;align-items:center;position:relative;overflow-y:auto;">
        <button id="fechar-popup-anuncio" style="position:absolute;top:18px;right:28px;background:none;border:none;font-size:3rem;cursor:pointer;color:#888;z-index:2;">&times;</button>
        <div style="display:flex;flex-wrap:wrap;gap:32px;justify-content:center;align-items:center;width:100%;">
          <?php foreach ($anuncios_destaque as $an): ?>
            <a href="<?php echo htmlspecialchars($an['link']); ?>" target="_blank" style="background:#f8f8f8;border-radius:18px;box-shadow:0 4px 24px rgba(0,0,0,0.10);padding:32px 24px;display:flex;flex-direction:column;align-items:center;max-width:340px;min-width:260px;text-decoration:none;color:#222;transition:box-shadow 0.2s;">
              <?php if (!empty($an['imagem'])): ?>
                <img src="<?php echo htmlspecialchars($an['imagem']); ?>" alt="Banner" style="max-width:280px;max-height:120px;border-radius:12px;object-fit:cover;margin-bottom:18px;box-shadow:0 2px 12px rgba(0,0,0,0.10);">
              <?php endif; ?>
              <span style="font-size:1.3rem;font-weight:700;text-align:center;line-height:1.3;margin-bottom:8px;display:block;overflow-wrap:break-word;">
                <?php echo htmlspecialchars($an['texto']); ?>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <script>
    let popupDestaqueTimer = null;
    function mostrarPopupDestaque() {
      document.getElementById('popup-anuncio-destaque').style.display = 'flex';
    }
    function esconderPopupDestaque() {
      document.getElementById('popup-anuncio-destaque').style.display = 'none';
      clearTimeout(popupDestaqueTimer);
      popupDestaqueTimer = setTimeout(mostrarPopupDestaque, 30000); // 30 segundos
    }
    window.addEventListener('DOMContentLoaded', function() {
      <?php if (!empty($anuncios_destaque)): ?>
        setTimeout(mostrarPopupDestaque, 2000);
        document.getElementById('fechar-popup-anuncio').onclick = esconderPopupDestaque;
      <?php endif; ?>
    });
    </script>

    <!-- Scripts da página inicial - movidos para o final do body -->
    <script src="./scripts/index.js"></script>
    <!-- Script de atualização de moedas -->
    <script src="./scripts/moedas.js"></script>
    <script src="./scripts/previsao_tempo.js"></script>

</body>

</html>
