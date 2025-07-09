<?php
session_start();

include_once './config/config.php';
include_once './classes/Usuario.php';
include_once './classes/Noticias.php';
include_once './classes/Categoria.php';
include_once './classes/Profissao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$usuario = new Usuario($banco);
$noticias = new Noticias($banco);
$categoria = new Categoria($banco);
$profissao = new Profissao($banco);

if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    $usuario->deletar($id);
    header('Location: portal.php');
    exit();
}


$dados_usuario = $usuario->lerPorId($_SESSION['usuario_id']);
if ($dados_usuario) {
    $nome_usuario = $dados_usuario['nome'];
    $id_usuario = $dados_usuario['id'];
    
    // Verificar se o usuário tem profissão definida
    if (isset($dados_usuario['profissao']) && $dados_usuario['profissao']) {
        $profissao_usuario = $profissao->lerPorId($dados_usuario['profissao']);
        $tipo_usuario = $profissao_usuario['nome'] ?? 'Usuário';
    } else {
        $tipo_usuario = 'Usuário';
    }
} else {
    header('Location: logout.php');
    exit();
}

$dados = $usuario->ler();
$noticias_usuario = $noticias->lerPorAutor($id_usuario);

// Carregar categorias para o filtro
if (method_exists($categoria, 'lerTodas')) {
    $categorias = $categoria->lerTodas();
} else {
    $categorias = $categoria->ler();
}

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
$filtro_titulo = isset($_GET['titulo']) ? trim($_GET['titulo']) : '';
$filtro_categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

$query = "SELECT * FROM noticias WHERE 1=1";
$params = [];
if ($filtro_titulo !== '') {
    $query .= " AND titulo LIKE ?";
    $params[] = "%$filtro_titulo%";
}

if ($filtro_categoria !== '') {
    $query .= " AND categoria_id = ?";
    $params[] = $filtro_categoria;
}
$query .= " ORDER BY data DESC, id DESC";
$stmt = $banco->prepare($query);
$stmt->execute($params);
$todas_noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

?>
<!-- ====== INÍCIO DO HTML ====== -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- ====== METADADOS E ESTILOS ====== -->
    <meta charset="UTF-8">
    <title>CSL Times - Portal</title>
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="./uploads/portal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body class="portal-body">
    <button id="toggle-theme" class="theme-toggle-btn" title="Alternar tema">
      <i class="fa-solid fa-moon"></i>
    </button>
    <!-- ====== CABEÇALHO DO PORTAL ====== -->
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

    <!-- ====== CONTEÚDO PRINCIPAL ====== -->
    <div class="portal-container">
        <div class="portal-add-btn-container">
        <?php if (strtolower($tipo_usuario) === 'jornalista'): ?>
            <a href="cadastrarNoticia.php" class="portal-add-btn"><i class="fas fa-plus"></i> Adicionar Notícia</a>
        <?php else: ?>
            <!-- Para outros tipos de usuário, mostra ambos os botões -->
            <a href="cadastrarNoticia.php" class="portal-add-btn"><i class="fas fa-plus"></i> Adicionar Notícia</a>
            <a href="cadastrarAnuncio.php" class="portal-add-btn"><i class="fas fa-plus"></i> Adicionar Anúncio</a>
        <?php endif; ?>
        </div>
        
        <h2 class="portal-section-title">
            <?php if (strtolower($tipo_usuario) === 'jornalista'): ?>
                Suas Notícias
            <?php elseif (strtolower($tipo_usuario) === 'anunciante'): ?>
                Seus Anúncios
            <?php else: ?>
                Suas Publicações
            <?php endif; ?>
        </h2>
        
        <?php if (empty($noticias_usuario)): ?>
            <div class="empty-state">
                <?php if (strtolower($tipo_usuario) === 'jornalista'): ?>
                    <p>Você ainda não publicou nenhuma notícia.</p>
                <?php elseif (strtolower($tipo_usuario) === 'anunciante'): ?>
                    <p>Você ainda não publicou nenhum anúncio.</p>
                <?php else: ?>
                    <p>Você ainda não publicou nenhuma publicação.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
               <!-- Filtro de busca -->
               <form method="get" style="display: flex; gap: 8px; justify-content: center; align-items: center; margin-bottom: 18px;">
                        <input type="text" name="titulo" value="<?= htmlspecialchars($filtro_titulo) ?>" placeholder="Título" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #bbb; font-size: 0.95rem; min-width: 120px;">
                        <select name="categoria" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #bbb; font-size: 0.95rem;">
                            <option value="">Categoria</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $filtro_categoria == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="padding: 4px 16px; border-radius: 4px; background: #2196f3; color: #fff; border: none; font-size: 0.95rem; cursor: pointer;">Filtrar</button>
                    </form>
   
            <div class="news-grid">
                <?php foreach ($noticias_usuario as $noticia): 
                    $cat = $categoria->lerPorId($noticia['categoria']);
                    $noticia['categoria_nome'] = $cat['nome'] ?? 'Sem categoria';
                ?>
            
                    <article class="news-card" onclick="abrirModalNoticia(<?php echo htmlspecialchars(json_encode($noticia)); ?>)">
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
                        <div class="news-card-actions" onclick="event.stopPropagation();">
                            <a href="alterarNoticia.php?id=<?php echo $noticia['id']; ?>" class="news-edit-btn" title="Editar Notícia"><i class="fas fa-edit"></i></a>
                            <a href="deletarNoticia.php?id=<?php echo $noticia['id']; ?>" class="news-delete-btn" title="Excluir Notícia"><i class="fas fa-trash-alt"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para exibir a notícia completa -->
    <div id="modalNoticia" class="modal-noticia">
        <div class="modal-noticia-content">
            <span class="close-modal-noticia" onclick="fecharModalNoticia()">&times;</span>
            <div id="modalNoticiaContent">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <script src="./scripts/portal.js"></script>

</body>
</html>