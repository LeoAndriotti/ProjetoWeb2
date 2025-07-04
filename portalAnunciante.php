<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';
include_once './classes/Anuncio.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$usuario = new Usuario($banco);
$anuncio = new Anuncio($banco);

$dados_usuario = $usuario->lerPorId($_SESSION['usuario_id']);
if ($dados_usuario) {
    $nome_usuario = $dados_usuario['nome'];
    $id_usuario = $dados_usuario['id'];
} else {
    header('Location: logout.php');
    exit();
}

$anuncios_usuario = $anuncio->lerPorNomeAnunciante($nome_usuario);

function saudacao() {
    $hora = date('H');
    if ($hora >= 6 && $hora < 12) {
        return "Bom dia";
    } elseif ($hora >= 12 && $hora < 18) {
        return "Boa tarde";
    } else {
        return "Boa noite";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CSL Times - Portal Anunciante</title>
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="./uploads/portalAnunciante.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body class="portal-body">
    <button id="toggle-theme" class="theme-toggle-btn" title="Alternar tema">
      <i class="fa-solid fa-moon"></i>
    </button>
    <div class="portal-header portal-header-portal">
        <img src="./assets/img/logo2.png" alt="CSL Times" class="portal-logo-img" style="width: 150px; height: 130px;">
        <div class="portal-header-content">
            <h1><span class="saudacao-portal"><?php echo saudacao(); ?></span>, <?php echo $nome_usuario; ?>!</h1>
            <div class="portal-nav">
                <a href="alterar.php?id=<?php echo $id_usuario; ?>"><i class="fas fa-user-edit"></i> Editar Usuário</a>
                <a href="indexUsuario.php"><i class="fas fa-sign-out-alt"></i> Voltar</a>
            </div>
        </div>
    </div>
    <div class="portal-container">
        <div class="portal-add-btn-container">
            <a href="cadastrarAnuncio.php" class="portal-add-btn"><i class="fas fa-plus"></i> Adicionar Anúncio</a>
        </div>
        <h2 class="portal-section-title" style="color: white;">Seus Anúncios</h2>
        <?php if (empty($anuncios_usuario)): ?>
            <div class="empty-state">
                <p>Você ainda não publicou nenhum anúncio.</p>
            </div>
        <?php else: ?>
            <div class="news-grid">
                <?php foreach ($anuncios_usuario as $anuncio): ?>
                    <article class="news-card">
                        <?php if (!empty($anuncio['imagem'])): ?>
                            <div class="news-image">
                                <?php
                                $img = ltrim($anuncio['imagem'], '@');
                                if (strpos($img, 'http') === 0) {
                                    $src = $img;
                                } else {
                                    $src = 'uploads/' . $img;
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($anuncio['nome']); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="news-content">
                            <h3 class="news-title"><?php echo htmlspecialchars($anuncio['nome']); ?></h3>
                            <p class="news-excerpt"><?php echo htmlspecialchars(substr($anuncio['texto'], 0, 150)) . '...'; ?></p>
                            <div class="news-meta">
                                <div class="news-meta-top">
                                    <span class="news-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($anuncio['dataCadastro'])); ?>
                                    </span>
                                </div>
                                <span class="news-category">
                                    <i class="fas fa-money-bill-wave"></i>
                                    R$ <?php echo number_format($anuncio['valorAnuncio'], 2, ',', '.'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="news-card-actions" onclick="event.stopPropagation();">
                            <a href="alterarAnuncio.php?id=<?php echo $anuncio['id']; ?>" class="news-edit-btn" title="Editar Anúncio"><i class="fas fa-edit"></i></a>
                            <a href="deletarAnuncio.php?id=<?php echo $anuncio['id']; ?>" class="news-delete-btn" title="Excluir Anúncio"><i class="fas fa-trash-alt"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="./scripts/portalAnunciante.js"></script>
</body>
</html>
