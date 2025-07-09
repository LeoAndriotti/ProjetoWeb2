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

// Buscar um anúncio aleatório que seja ativo E destaque para o pop-up
$anuncio_aleatorio = null;
if (!empty($anuncios_destaque)) {
    $anuncio_aleatorio = $anuncios_destaque[array_rand($anuncios_destaque)];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSL Times</title>
    <!-- Estilos principais do site -->
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="./uploads/index.css">
    <link rel="stylesheet" href="./uploads/previsao_tempo.css">
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>

<body class="portal-body">
    <button id="toggle-theme-desktop" class="theme-toggle-btn" title="Alternar tema">
      <i class="fa-solid fa-moon"></i>
    </button>
    <div class="top-bar-mobile">
      <button id="toggle-theme" class="theme-toggle-btn" title="Alternar tema">
        <i class="fa-solid fa-moon"></i>
      </button>
      <button id="toggle-moedas" class="moedas-btn-mobile" title="Mostrar moedas e clima">
        <i class="fas fa-coins"></i>
      </button>
      <a href="logar.php" class="login-btn-mobile">Entrar</a>
    </div>
    <div id="moedas-mobile-container" class="moedas-mobile-container">
      <?php include './components/moedas.php'; ?>
    </div>
    
    <!-- Carrossel horizontal de anúncios para mobile -->
    <?php if (!empty($anuncios_ativos)): ?>
    <div class="anuncios-ticker-horizontal" id="anuncios-ticker-horizontal">
      <div class="anuncios-ticker-inner-horizontal">
        <?php foreach ($anuncios_ativos as $an): ?>
          <a href="<?php echo htmlspecialchars($an['link']); ?>" target="_blank" class="anuncio-horizontal-banner">
            <img src="<?php echo !empty($an['imagem']) ? htmlspecialchars($an['imagem']) : './assets/img/logo2.png'; ?>" alt="Banner" />
            <?php if (!empty($an['nome'])): ?>
              <span class="anuncio-horizontal-texto"><?php echo htmlspecialchars($an['nome']); ?></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
    
    <!-- Container com linhas verticais decorativas -->
    <div class="container-linhas-verticais">
      <div class="linha-vertical linha-vertical-esquerda"></div>
      <div class="linha-vertical linha-vertical-direita"></div>

      <!-- Botão de login fixo no topo direito -->
      <a href="logar.php" class="login-btn" style="position: fixed; right: 30px; top: 8px; z-index: 1100; font-size: 1rem; padding: 0.6rem 1.2rem;">Entrar</a>
      <!-- Cabeçalho principal (pode ser expandido para navegação) -->
      <header class="main-header">
          <div class="header-content">
              <!-- Espaço reservado para conteúdo do cabeçalho -->
          </div>
      </header>
      

      <!-- Carrosseis verticais de anúncios ativos nas laterais -->
      <?php if (!empty($anuncios_ativos)): ?>
      <div class="carrossel-vertical carrossel-vertical-esquerda">
        <div class="carrossel-vertical-inner" id="carrossel-vertical-esquerda">
          <?php foreach ($anuncios_ativos as $an): ?>
            <a href="<?php echo htmlspecialchars($an['link']); ?>" target="_blank" class="anuncio-vertical-banner">
              <img src="<?php echo !empty($an['imagem']) ? htmlspecialchars($an['imagem']) : './assets/img/logo2.png'; ?>" alt="Banner" />
              <?php if (!empty($an['nome'])): ?>
                <span class="anuncio-vertical-texto"><?php echo htmlspecialchars($an['nome']); ?></span>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="carrossel-vertical carrossel-vertical-direita">
        <div class="carrossel-vertical-inner" id="carrossel-vertical-direita">
          <?php foreach ($anuncios_ativos as $an): ?>
            <a href="<?php echo htmlspecialchars($an['link']); ?>" target="_blank" class="anuncio-vertical-banner">
              <img src="<?php echo !empty($an['imagem']) ? htmlspecialchars($an['imagem']) : './assets/img/logo2.png'; ?>" alt="Banner" />
              <?php if (!empty($an['nome'])): ?>
                <span class="anuncio-vertical-texto"><?php echo htmlspecialchars($an['nome']); ?></span>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

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
            <!-- Filtro de busca -->
      <form method="get" class="filtro-busca-form" style="gap: 0.5rem; padding: 0.7rem 1rem 0.7rem 1rem; max-width: 600px; font-size: 0.95rem;">
        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($filtro_titulo) ?>" placeholder="Título" style="min-width: 120px; font-size: 0.95rem; padding: 0.4rem 0.7rem; border-radius: 6px;">
        <select name="autor" id="autor" style="min-width: 100px; font-size: 0.95rem; padding: 0.4rem 0.7rem; border-radius: 6px;">
            <option value="">Autor</option>
            <?php foreach ($autores as $a): ?>
                <option value="<?= $a['id'] ?>" <?= $filtro_autor == $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="categoria" id="categoria" style="min-width: 100px; font-size: 0.95rem; padding: 0.4rem 0.7rem; border-radius: 6px;">
            <option value="">Categoria</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $filtro_categoria == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nome']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="submit-btn" style="height: 32px; font-size: 0.95rem; padding: 0 16px; border-radius: 6px;"><i class="fa-solid fa-filter"></i>Buscar</button>
      </form>
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
    </div>

    <!-- Pop-up de anúncio aleatório -->
    <?php if ($anuncio_aleatorio): ?>
    <div id="popup-anuncio-aleatorio" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.85);z-index:10000;justify-content:center;align-items:center;">
      <div style="background:#fff;padding:40px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.5);max-width:600px;max-height:80vh;display:flex;flex-direction:column;align-items:center;position:relative;overflow-y:auto;text-align:center;">
        <button id="fechar-popup-aleatorio" style="position:absolute;top:15px;right:20px;background:none;border:none;font-size:2.5rem;cursor:pointer;color:#666;z-index:2;">&times;</button>
        
        <div style="margin-bottom:20px;">
          <h3 style="color:#333;margin-bottom:15px;font-size:1.5rem;"><?php echo htmlspecialchars($anuncio_aleatorio['nome']); ?></h3>
          
          <?php if (!empty($anuncio_aleatorio['imagem'])): ?>
            <img src="<?php echo htmlspecialchars($anuncio_aleatorio['imagem']); ?>" alt="Anúncio" style="max-width:100%;max-height:300px;border-radius:12px;object-fit:cover;margin-bottom:20px;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
          <?php endif; ?>
          
          <?php if (!empty($anuncio_aleatorio['texto'])): ?>
            <p style="color:#555;line-height:1.6;margin-bottom:25px;font-size:1.1rem;"><?php echo htmlspecialchars($anuncio_aleatorio['texto']); ?></p>
          <?php endif; ?>
          
          <?php if (!empty($anuncio_aleatorio['link'])): ?>
            <a href="<?php echo htmlspecialchars($anuncio_aleatorio['link']); ?>" target="_blank" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);color:#fff;padding:12px 30px;border-radius:25px;text-decoration:none;font-weight:600;transition:all 0.3s ease;box-shadow:0 4px 15px rgba(102,126,234,0.4);">
              Ver Mais <i class="fa-solid fa-external-link-alt" style="margin-left:8px;"></i>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Scripts da página inicial - movidos para o final do body -->
    <script src="./scripts/index.js"></script>
    <!-- Script de atualização de moedas -->
    <script src="./scripts/moedas.js"></script>
    <script src="./scripts/previsao_tempo.js"></script>

</body>

</html>
