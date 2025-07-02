<?php
include_once 'components/previsao_tempo.php';
// Inicia a sessão para controle de login
session_start();
// Inclui arquivos de configuração e classes principais
include_once 'config/config.php'; // Configurações do banco de dados
include_once 'classes/Usuario.php'; // Classe de usuário
include_once 'classes/Noticias.php'; // Classe de notícias
include_once 'classes/Categoria.php'; // Classe de categorias
// Define constante para identificar que estamos na página inicial (index)
define('INDEX_MODE', true);
// Inclui o componente de card de notícia reutilizável
include_once 'components/noticiaCard.php';

// Instancia os objetos principais
$usuario = new Usuario($banco);
$noticias = new Noticias($banco);
$categoria = new Categoria($banco);

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
    <!-- Scripts da página inicial -->
    <script src="./scripts/Index.js"></script>
    <!-- Script de atualização de moedas -->
    <script src="./scripts/moedas.js"></script>
    <script src="./scripts/previsao_tempo.js"></script>
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

</body>

</html>
<script>
const btn = document.getElementById('toggle-theme');
function updateThemeBtn() {
  if (document.body.classList.contains('dark-mode')) {
    btn.innerHTML = '<i class="fa-solid fa-sun"></i>';
  } else {
    btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
  }
}
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
const savedTheme = localStorage.getItem('theme');
if (savedTheme) {
  document.body.classList.toggle('dark-mode', savedTheme === 'dark');
  document.body.classList.toggle('light-mode', savedTheme === 'light');
} else if (prefersDark) {
  document.body.classList.add('dark-mode');
} else {
  document.body.classList.add('light-mode');
}
updateThemeBtn();
btn.onclick = function() {
  document.body.classList.toggle('dark-mode');
  document.body.classList.toggle('light-mode');
  const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
  localStorage.setItem('theme', theme);
  updateThemeBtn();
};
</script>