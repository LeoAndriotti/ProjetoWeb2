<?php
session_start();
include_once './config/config.php';  
include_once './classes/Anuncio.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anuncio = new Anuncio($banco);
    $nome = $_POST['nome'];
    $imagem = $_POST['imagem'];
    $link = $_POST['link'];
    $texto = $_POST['texto'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $data_cadastro = date('Y-m-d H:i:s');
    $valorAnuncio = $_POST['valorAnuncio'];
    
    // Limpar a máscara do valor (remove R$, espaços e converte vírgula para ponto)
    $valorAnuncio = str_replace(['R$', ' ', '.'], '', $valorAnuncio);
    $valorAnuncio = str_replace(',', '.', $valorAnuncio);
    
    // Se o valor estiver vazio, definir como 0
    if (empty($valorAnuncio)) {
        $valorAnuncio = 0;
    }

    $anuncio->criar($nome, $imagem, $link, $ativo, $destaque, $data_cadastro, $valorAnuncio, $texto);
    header('Location: portal.php');
    exit();
}

$sql = "SELECT id, nome FROM usuarios ORDER BY nome ASC";
$stmt = $banco->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlCat = "SELECT id,nome FROM categorias ORDER BY nome ASC";
$stmt = $banco->query($sqlCat);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSL Times - Novo Anúncio</title>
    <!-- Estilos principais do site -->
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="./uploads/cadastrarAnuncio.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body class="portal-body">
    <!-- Botão de dark mode -->
    <button id="toggle-theme" class="theme-toggle-btn" title="Alternar tema">
      <i class="fa-solid fa-moon"></i>
    </button>

    <!-- ====== CABEÇALHO ====== -->
    <div class="portal-header">
    <img src="./assets/img/logo2.png" alt="CSL Times" class="portal-logo-img">

        <div class="portal-nav">
            <a href="portal.php"><i class="fas fa-arrow-left"></i> Voltar ao Portal</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </div>

    <!-- ====== FORMULÁRIO DE ANÚNCIO ====== -->
    <div class="portal-container">
        <div class="form-card">
            <form method="POST" class="portal-form">
                <div class="form-header">
                    <i class="fas fa-ad"></i>
                    <h2>Novo Anúncio</h2>
                </div>
                <div class="form-group">
                    <label for="nome"><i class="fas fa-user-tag"></i> Nome do Anunciante</label>
                    <input type="text" name="nome" id="nome" placeholder="Nome da empresa ou anunciante" required>
                </div>
                <div class="form-group">
                    <label for="imagem"><i class="fas fa-image"></i> Imagem/Banner (URL)</label>
                    <input type="url" name="imagem" id="imagem" placeholder="URL da imagem/banner" required>
                </div>
                <div class="form-group">
                    <label for="link"><i class="fas fa-link"></i> Link de Destino</label>
                    <input type="url" name="link" id="link" placeholder="URL de destino (ex: site, promoção)" required>
                </div>
                <div class="form-group">
                    <label for="texto"><i class="fas fa-quote-right"></i> Mensagem/Slogan</label>
                    <input type="text" name="texto" id="texto" placeholder="Mensagem ou slogan do anúncio" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="valorAnuncio"><i class="fas fa-money-bill-wave"></i> Valor do Anúncio (R$)</label>
                    <input type="text" name="valorAnuncio" id="valorAnuncio" placeholder="R$ 0,00" required>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 2rem; margin-top: 2.2rem;">
                        <label style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="ativo" id="ativo" style="width: 18px; height: 18px;"> Ativo
                        </label>
                        <label style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="destaque" id="destaque" style="width: 18px; height: 18px;"> Destaque
                        </label>
                    </div>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-plus"></i> Publicar Anúncio
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts movidos para o final do body -->
    <script src="./scripts/cadastrarAnuncio.js"></script>
</body>
</html>
