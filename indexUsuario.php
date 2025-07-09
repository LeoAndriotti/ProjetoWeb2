<?php
include_once 'components/previsao_tempo.php';
// Inicia a sessão para garantir que o usuário está logado
session_start();
// Inclui arquivos de configuração e classes principais
include_once 'config/config.php'; // Configurações do banco de dados
include_once 'classes/Usuario.php'; // Classe de usuário
include_once 'classes/Noticias.php'; // Classe de notícias
include_once 'classes/Categoria.php'; // Classe de categorias
include_once 'classes/Anuncio.php'; // Classe de anúncios
// Inclui o componente de card de notícia reutilizável
include_once 'components/noticiaCard.php';

// Instancia os objetos principais
$usuario = new Usuario($banco);
$noticias = new Noticias($banco);
$categoria = new Categoria($banco);
$anuncio = new Anuncio($banco);

// Recupera o usuário logado
$tipo_usuario = 'usuario'; // valor padrão
if (isset($_SESSION['usuario_id'])) {
    $dados_usuario = $usuario->lerPorId($_SESSION['usuario_id']);
    if ($dados_usuario && isset($dados_usuario['profissao'])) {
        // Se tiver profissão, busque o nome da profissão
        include_once 'classes/Profissao.php';
        $profissao = new Profissao($banco);
        $profissao_usuario = $profissao->lerPorId($dados_usuario['profissao']);
        $tipo_usuario = strtolower($profissao_usuario['nome'] ?? 'usuario');
    }
}

// Busca todas as notícias do banco
$todas_noticias = $noticias->ler();
$anuncios_ativos = $anuncio->lerAtivos();

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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSL Times - Usuário</title>
    <!-- Estilos principais do site -->
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="./uploads/Index.css">
    <link rel="stylesheet" href="./uploads/indexUsuario.css">
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
      <?php if (strtolower($tipo_usuario) === 'jornalista'): ?>
        <a href="portal.php" class="login-btn-mobile">Meu Portal</a>
      <?php else: ?>
        <a href="portalAnunciante.php" class="login-btn-mobile">Meu Portal</a>
      <?php endif; ?>
      <a href="logout.php" class="login-btn-mobile" style="margin-left: 8px; background: var(--btn-danger);">Sair</a>
    </div>
    <div id="moedas-mobile-container" class="moedas-mobile-container moedas-container-unico">
      <?php include './components/moedas.php'; ?>
    </div>
    <!-- Carrossel horizontal de anúncios abaixo do header removido -->
    <!-- Menu de moedas (reutilizável) -->
    <!-- Botões de navegação do usuário no desktop -->
    <div class="usuario-nav-desktop">
      <?php if (strtolower($tipo_usuario) === 'jornalista'): ?>
        <a href="portal.php" class="login-btn">Meu Portal</a>
      <?php else: ?>
        <a href="portalAnunciante.php" class="login-btn">Meu Portal</a>
      <?php endif; ?>
      <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <!-- Cabeçalho principal (pode ser expandido para navegação) -->
    <header class="main-header">
        <div class="header-content">
            <!-- Espaço reservado para conteúdo do cabeçalho -->
        </div>
    </header>

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
            <?php else: ?>
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
                <div class="news-grid">
                    <!-- Renderiza cada notícia usando o componente reutilizável -->
                    <?php foreach ($todas_noticias as $noticia): ?>
                        <?php echo renderNoticiaCard($noticia, $usuario, $categoria); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Modal para exibir a notícia completa -->
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

    <!-- Scripts da área do usuário - movidos para o final do body -->
    <script src="./scripts/indexUsuario.js"></script>
    <!-- Script de atualização de moedas -->
    <script src="./scripts/moedas.js"></script>
    <script src="./scripts/previsao_tempo.js"></script>
</body>
</html>
