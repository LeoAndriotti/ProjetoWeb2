<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

include_once 'config/config.php';
include_once 'classes/Anuncio.php';
include_once 'classes/Usuario.php';

$anuncio = new Anuncio($banco);
$usuario = new Usuario($banco);

$mensagem = '';
$erro = '';

if (!isset($_GET['id'])) {
    header("Location: portalAnunciante.php");
    exit();
}

$id = $_GET['id'];
$dados_anuncio = $anuncio->lerPorId($id);

if (!$dados_anuncio) {
    header("Location: portalAnunciante.php");
    exit();
}

// Verificar se o anúncio pertence ao usuário logado
$dados_usuario = $usuario->lerPorId($_SESSION['usuario_id']);
if ($dados_anuncio['nome'] !== $dados_usuario['nome']) {
    header("Location: portalAnunciante.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $imagem = $_POST['imagem'];
    $link = $_POST['link'];
    $texto = $_POST['texto'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $valorAnuncio = $_POST['valorAnuncio'];
    
    // Limpar a máscara do valor (remove R$, espaços e converte vírgula para ponto)
    $valorAnuncio = str_replace(['R$', ' ', '.'], '', $valorAnuncio);
    $valorAnuncio = str_replace(',', '.', $valorAnuncio);
    
    // Se o valor estiver vazio, definir como 0
    if (empty($valorAnuncio)) {
        $valorAnuncio = 0;
    }
    
    if ($anuncio->atualizar($id, $nome, $imagem, $link, $ativo, $destaque, $dados_anuncio['dataCadastro'], $valorAnuncio, $texto)) {
        header("Location: portalAnunciante.php");
        exit();
    } else {
        $erro = "Erro ao atualizar o anúncio.";
    }
}

$nome_usuario = $dados_usuario ? $dados_usuario['nome'] : '';

function saudacao() {
    return "Boa noite";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Anúncio - CSL Times</title>
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="./uploads/alterarAnuncio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body class="portal-body">
    <!-- Botão de dark mode -->
    <button id="toggle-theme" class="theme-toggle-btn" title="Alternar tema">
      <i class="fa-solid fa-moon"></i>
    </button>

    <!-- ====== CABEÇALHO ====== -->
    <div class="portal-header portal-header-portal">
        <img src="./assets/img/logo2.png" alt="CSL Times" class="portal-logo-img" style="width: 150px; height: 130px;">
        <div class="portal-header-content">
            <h1><span class="saudacao-portal"><?php echo saudacao(); ?></span>, <?php echo ucwords(strtolower($nome_usuario)); ?>!</h1>
            <div class="portal-nav">
                <a href="portalAnunciante.php"><i class="fas fa-arrow-left"></i> Voltar ao Portal</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </div>

    <!-- ====== FORMULÁRIO DE EDIÇÃO DE ANÚNCIO ====== -->
    <div class="portal-container">
        <div class="form-card">
            <form method="POST" class="portal-form">
                <div class="form-header">
                    <i class="fas fa-edit"></i>
                    <h2>Editar Anúncio</h2>
                </div>
                
                <?php if ($erro): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="nome"><i class="fas fa-user-tag"></i> Nome do Anunciante</label>
                    <input type="text" name="nome" id="nome" placeholder="Nome da empresa ou anunciante" required value="<?php echo htmlspecialchars($dados_anuncio['nome']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="imagem"><i class="fas fa-image"></i> Imagem/Banner (URL)</label>
                    <input type="url" name="imagem" id="imagem" placeholder="URL da imagem/banner" required value="<?php echo htmlspecialchars($dados_anuncio['imagem']); ?>">
                </div>
                <div class="form-group">
                    <label for="link"><i class="fas fa-link"></i> Link de Destino</label>
                    <input type="url" name="link" id="link" placeholder="URL de destino (ex: site, promoção)" required value="<?php echo htmlspecialchars($dados_anuncio['link']); ?>">
                </div>
                <div class="form-group">
                    <label for="texto"><i class="fas fa-quote-right"></i> Mensagem/Slogan</label>
                    <input type="text" name="texto" id="texto" placeholder="Mensagem ou slogan do anúncio" required value="<?php echo htmlspecialchars($dados_anuncio['texto']); ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="valorAnuncio"><i class="fas fa-money-bill-wave"></i> Valor do Anúncio (R$)</label>
                        <input type="text" name="valorAnuncio" id="valorAnuncio" placeholder="R$ 0,00" required value="R$ <?php echo number_format($dados_anuncio['valorAnuncio'], 2, ',', '.'); ?>">
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 2rem; margin-top: 2.2rem;">
                        <label style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="ativo" id="ativo" style="width: 18px; height: 18px;" <?php echo $dados_anuncio['ativo'] ? 'checked' : ''; ?>> Ativo
                        </label>
                        <label style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="destaque" id="destaque" style="width: 18px; height: 18px;" <?php echo $dados_anuncio['destaque'] ? 'checked' : ''; ?>> Destaque
                        </label>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="portalAnunciante.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts movidos para o final do body -->
    <script src="./scripts/alterarAnuncio.js"></script>
</body>
</html>
