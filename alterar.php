<?php
session_start();

// Verifica se o usuário está logado, caso contrário redireciona para index
if(!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

// Inclui arquivos de configuração e classes necessárias
include_once './config/config.php';      // Configurações do banco de dados
include_once './classes/Usuario.php';    // Classe para operações com usuários

// Instancia o objeto usuário para operações no banco
$usuario = new Usuario($banco);

// Verifica se o ID foi fornecido e se corresponde ao usuário logado
// Isso impede que um usuário edite dados de outro usuário
if (!isset($_GET['id']) || $_GET['id'] != $_SESSION['usuario_id']) {
    header('Location: portal.php');
    exit();
}

// Obtém o ID do usuário da URL
$id = $_GET['id'];

// Busca os dados do usuário no banco de dados
$linha = $usuario->lerPorId($id);

// Se não encontrar o usuário, redireciona para o portal
if (!$linha) {
    header('Location: portal.php');
    exit();
}

// ====== PROCESSAMENTO DO FORMULÁRIO ======
// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém e sanitiza os dados do formulário
    $nome = $_POST['nome'];
    $sexo = $_POST['sexo'];
    $fone = $_POST['fone'];
    $email = $_POST['email'];
    
    // Atualiza os dados do usuário no banco
    $usuario->atualizar($id, $nome, $sexo, $fone, $email);
    
    // Redireciona para o portal após salvar
    header('Location: portal.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário - CSL Times</title>
    
    <!-- Folhas de estilo -->
    <link rel="stylesheet" href="./uploads/style.css">      <!-- Estilos globais -->
    <link rel="stylesheet" href="./uploads/alterar.css">    <!-- Estilos específicos desta página -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
    
    <!-- Script JavaScript específico para esta página -->
    <script src="./scripts/alterar.js"></script>
</head>
<body class="portal-body">
    <div class="portal-header portal-header-portal">
        <!-- Logo da empresa -->
        <img src="./assets/img/logo2.png" alt="CSL Times" class="portal-logo-img">

        <div class="portal-header-content">
            <!-- Título da página -->
            <h1>Editar Usuário</h1>
            
            <!-- Navegação - Botão para voltar ao portal -->
            <div class="portal-nav">
                <a href="portal.php"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>

    <div class="form-card">
        <!-- Formulário principal com método POST -->
        <form method="POST" class="portal-form">
            <!-- Cabeçalho do formulário -->
            <div class="form-header">
                <i class="fa-solid fa-user-edit"></i>
                <h2>Editar Dados do Usuário</h2>
            </div>

            <!-- Campo oculto com ID do usuário -->
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($linha['id']); ?>">
            
            <div class="form-group">
                <label for="nome"><i class="fas fa-user"></i> Nome:</label>
                <input type="text" 
                       name="nome" 
                       id="nome" 
                       value="<?php echo htmlspecialchars($linha['nome']); ?>" 
                       required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-venus-mars"></i> Sexo:</label>
                <div class="form-row">
                    <label for="masculino_editar">
                        <input type="radio" 
                               id="masculino_editar" 
                               name="sexo" 
                               value="M" 
                               <?php echo ($linha['sexo'] === 'M') ? 'checked' : ''; ?> 
                               required>
                        <i class="fa-solid fa-mars"></i> Masculino
                    </label>
                    
                    <label for="feminino_editar">
                        <input type="radio" 
                               id="feminino_editar" 
                               name="sexo" 
                               value="F" 
                               <?php echo ($linha['sexo'] === 'F') ? 'checked' : ''; ?> 
                               required>
                        <i class="fa-solid fa-venus"></i> Feminino
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="fone"><i class="fas fa-phone"></i> Telefone:</label>
                <input type="text" 
                       name="fone" 
                       id="fone" 
                       value="<?php echo htmlspecialchars($linha['fone']); ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="<?php echo htmlspecialchars($linha['email']); ?>" 
                       required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
        </form>
    </div>
</body>
</html>