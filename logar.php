<?php
// Inicia a sessão para controle de login
session_start();
// Inclui arquivos de configuração e classe de usuário
include_once 'config/config.php'; // Configurações do banco de dados
include_once 'classes/Usuario.php'; // Classe de usuário

// Instancia o objeto de usuário
$usuario = new Usuario($banco);
$erro_login = '';

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
    <title>Login - CSL Times</title>
    <!-- Estilos principais do site -->
    <link rel="stylesheet" href="uploads/style.css">
    <link rel="stylesheet" href="uploads/logar.css">
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <link rel="icon" href="./assets/img/logo.png" type="image/png">
     <script src="./scripts/logar.js"></script>
</head>
<body class="portal-body">
   
    <!-- Modal de login centralizado -->
    <div class="modal active" id="loginModal">
        <div class="modal-content">
            <!-- Botão para fechar o modal -->
            <span class="close-modal" onclick="window.location.href='index.php'">&times;</span>
            <!-- Formulário de login -->
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required placeholder="Seu email">
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" required placeholder="Sua senha">
                </div>
                <button type="submit" name="entrar" class="submit-btn">Entrar</button>
            </form>
            <!-- Links para registro e recuperação de senha -->
            <div class="register-link">
                <p style="color: black;">Não tem uma conta? <a href="./registrar.php">Registre-se aqui</a></p>
                <p style="color: black;">Esqueceu a senha? <a href="./alterarSenha.php">Alterar Senha</a></p>
            </div>
            <!-- Mensagem de erro, se houver -->
            <?php if (!empty($erro_login)): ?>
                <div class="login-error"><?php echo $erro_login; ?></div>
            <?php endif; ?>
        </div>
    </div>
 
</body>
</html>
