<?php
// Função responsável por renderizar o card de uma notícia
// Recebe: notícia (array), usuário (objeto), categoria (objeto)
function renderNoticiaCard($noticia, $usuario, $categoria) {
    // Busca o autor da notícia pelo ID
    $autor = $usuario->lerPorId($noticia['autor']);
    // Busca a categoria da notícia pelo ID
    $cat = $categoria->lerPorId($noticia['categoria']);
    // Prepara o caminho da imagem (se houver)
    $img = ltrim($noticia['imagem'], '@');
    $src = (strpos($img, 'http') === 0) ? $img : 'uploads/' . $img;
    // Inicia o buffer de saída para retornar o HTML como string
    ob_start();
    ?>
    <article class="news-card"
        <?php // Se estiver no index.php, mostra alerta ao clicar; senão, abre o modal de notícia ?>
        <?php if(defined('INDEX_MODE') && INDEX_MODE): ?>
            onclick="noticiaCardClickAlert()"
        <?php else: ?>
            onclick="abrirModalNoticia(<?php echo htmlspecialchars(json_encode($noticia)); ?>, '<?php echo htmlspecialchars($autor['nome'] ?? 'Autor desconhecido'); ?>', '<?php echo htmlspecialchars($cat['nome'] ?? 'Sem categoria'); ?>')"
        <?php endif; ?>
    >
        <?php if (!empty($noticia['imagem'])): ?>
            <div class="news-image">
                <!-- Exibe a imagem da notícia -->
                <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
            </div>
        <?php endif; ?>
        <div class="news-content">
            <!-- Título da notícia -->
            <h3 class="news-title"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
            <!-- Pequeno resumo da notícia -->
            <p class="news-excerpt"><?php echo htmlspecialchars(substr($noticia['noticia'], 0, 150)) . '...'; ?></p>
            <div class="news-meta">
                <div class="news-meta-top">
                    <!-- Nome do autor -->
                    <span class="news-author">
                        <i class="fas fa-user"></i>
                        <?php echo htmlspecialchars($autor['nome'] ?? 'Autor desconhecido'); ?>
                    </span>
                    <!-- Data da notícia -->
                    <span class="news-date">
                        <i class="fas fa-calendar"></i>
                        <?php echo date('d/m/Y', strtotime($noticia['data'])); ?>
                    </span>
                </div>
                <!-- Categoria da notícia -->
                <span class="news-category">
                    <i class="fas fa-tag"></i>
                    <?php echo htmlspecialchars($cat['nome'] ?? 'Sem categoria'); ?>
                </span>
            </div>
        </div>
    </article>
    <?php
    // Retorna o HTML gerado como string
    return ob_get_clean();
} 