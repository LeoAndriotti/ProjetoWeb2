<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';
include_once './classes/Noticias.php';
include_once './classes/Categoria.php';
// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}
$usuario = new Usuario($banco);
$noticias = new Noticias($banco);
$categoria = new Categoria($banco);
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
} else {
    header('Location: logout.php');
    exit();
}
$dados = $usuario->ler();
$noticias_usuario = $noticias->lerPorAutor($id_usuario);
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
<!-- ====== INÍCIO DO HTML ====== -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- ====== METADADOS E ESTILOS ====== -->
    <meta charset="UTF-8">
    <title>CSL Times - Portal</title>
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
    <style>
    @media (max-width: 768px) {
        .portal-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        .portal-nav {
            width: 100%;
            justify-content: center;
        }
        .portal-table {
            display: block;
            overflow-x: auto;
        }
        .form-card, .portal-form {
            padding: 1rem !important;
            max-width: 98vw !important;
        }
        .form-header h2 {
            font-size: 1.2rem;
        }
        .form-header i {
            font-size: 1.5rem;
        }
        .news-grid {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
        .news-card {
            max-width: 98vw !important;
            margin: 0 auto !important;
        }
        .modal-noticia-content {
            width: 98vw !important;
            max-width: 98vw !important;
            padding: 1rem !important;
        }
        .modal-noticia-header h2 {
            font-size: 1.2rem !important;
        }
    }
    </style>
</head>
<body class="portal-body">
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
        <a href="cadastrarNoticia.php" class="portal-add-btn"><i class="fas fa-plus"></i> Adicionar Notícia</a>
        <h2 class="portal-section-title">Suas Notícias</h2>
        <?php if (empty($noticias_usuario)): ?>
            <div class="empty-state">
                <p>Você ainda não publicou nenhuma notícia.</p>
            </div>
        <?php else: ?>
            <div class="news-grid">
                <?php foreach ($noticias_usuario as $noticia): ?>
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

    <!-- ====== SCRIPTS ====== -->
    <script>
        function abrirModalNoticia(noticia) {
            const modal = document.getElementById('modalNoticia');
            const modalContent = document.getElementById('modalNoticiaContent');
            
            // Formatar a data
            const data = new Date(noticia.data);
            const dataFormatada = data.toLocaleDateString('pt-BR');
            
            // Determinar a URL da imagem
            let imgSrc = '';
            if (noticia.imagem) {
                const img = noticia.imagem.startsWith('@') ? noticia.imagem.substring(1) : noticia.imagem;
                if (img.startsWith('http')) {
                    imgSrc = img;
                } else {
                    imgSrc = 'uploads/' + img;
                }
            }
            
            let html = `
                <div class="modal-noticia-header">
                    <h2>${noticia.titulo}</h2>
                    <div class="modal-noticia-meta">
                        <span class="modal-noticia-date">
                            <i class="fas fa-calendar"></i> ${dataFormatada}
                        </span>
                        <span class="modal-noticia-category">
                            <i class="fas fa-tag"></i> 
                            <?php 
                                $cat = $categoria->lerPorId($noticia.categoria);
                                echo htmlspecialchars($cat['nome'] ?? 'Sem categoria');
                            ?>
                        </span>
                    </div>
                </div>
            `;
            
            if (imgSrc) {
                html += `
                    <div class="modal-noticia-image">
                        <img src="${imgSrc}" alt="${noticia.titulo}">
                    </div>
                `;
            }
            
            html += `
                <div class="modal-noticia-body">
                    <p>${noticia.noticia}</p>
                </div>
            `;
            
            modalContent.innerHTML = html;
            modal.style.display = 'flex';
        }

        function fecharModalNoticia() {
            document.getElementById('modalNoticia').style.display = 'none';
        }

        // Fechar modal ao clicar fora
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modalNoticia');
            if (event.target === modal) {
                fecharModalNoticia();
            }
        });

        // Fechar modal com ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                fecharModalNoticia();
            }
        });
    </script>
</body>
</html>