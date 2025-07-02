<?php
session_start();
include_once './config/config.php';  
include_once './classes/Noticias.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noticias = new Noticias($banco);
    $titulo = $_POST['titulo'];
    $noticia = $_POST['noticia'];
    $data = $_POST['data'];
    $autor = $_SESSION['usuario_id'];
    $id_categoria = $_POST['categoria'];
    $imagem = '';

    // Prioriza upload
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $diretorio_upload = 'uploads/';
        if (!file_exists($diretorio_upload)) {
            mkdir($diretorio_upload, 0777, true);
        }
        $extensao_arquivo = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $novo_nome_arquivo = uniqid() . '.' . $extensao_arquivo;
        $caminho_upload = $diretorio_upload . $novo_nome_arquivo;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_upload)) {
            $imagem = $novo_nome_arquivo;
        }
    } elseif (!empty($_POST['imagem_url'])) {
        // Se não houver upload, usa a URL
        $imagem = trim($_POST['imagem_url']);
    }

    $noticias->criar($titulo, $noticia, $data, $autor, $id_categoria, $imagem);
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
    <link rel="stylesheet" href="./uploads/style.css">
    <link rel="stylesheet" href="./uploads/cadastrarNoticia.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>
<body class="portal-body">
    <!-- ====== CABEÇALHO ====== -->
    <div class="portal-header">
    <img src="./assets/img/logo2.png" alt="CSL Times" class="portal-logo-img">

        <div class="portal-nav">
            <a href="portal.php"><i class="fas fa-arrow-left"></i> Voltar ao Portal</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </div>

    <!-- ====== FORMULÁRIO DE NOTÍCIA ====== -->
    <div class="portal-container">
        <div class="form-card">
            <form method="POST" class="portal-form" enctype="multipart/form-data">
                <div class="form-header">
                    <i class="fas fa-newspaper"></i>
                    <h2>Nova Notícia</h2>
                </div>

                <div class="form-group">
                    <label for="titulo">
                        <i class="fas fa-heading"></i> Título
                    </label>
                    <input type="text" name="titulo" id="titulo" placeholder="Digite o título da notícia" required>
                </div>

                <div class="form-group">
                    <label for="noticia">
                        <i class="fas fa-align-left"></i> Conteúdo
                    </label>
                    <textarea name="noticia" id="noticia" rows="8" placeholder="Digite o conteúdo da notícia" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="data">
                            <i class="fas fa-calendar"></i> Data
                        </label>
                        <input type="date" name="data" id="data" required>
                    </div>
                    <div class="form-group">
                        <label for="categoria">
                            <i class="fas fa-list"></i> Categoria
                        </label>
                        <select name="categoria" id="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categorias): ?>
                                <option value="<?= htmlspecialchars($categorias['id']) ?>">
                                    <?= htmlspecialchars($categorias['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="imagem">
                        <i class="fas fa-image"></i> Imagem de Capa (upload ou URL)
                    </label>
                    <div class="file-upload">
                        <input type="file" name="imagem" id="imagem" accept="image/*">
                        <label for="imagem" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Escolher arquivo</span>
                        </label>
                    </div>
                    <div style="margin-top: 1rem;">
                        <input type="url" name="imagem_url" id="imagem_url" placeholder="Ou cole a URL da imagem aqui">
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-plus"></i> Publicar Notícia
                </button>
            </form>
        </div>
    </div>

    <script src="./scripts/cadastrarNoticia.js"></script>
</body>
</html>