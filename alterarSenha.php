<?php
// Inicia a sessão para controle de usuário logado
session_start();
// Inclui arquivos de configuração e classe de usuário
include_once 'config/config.php'; // Configurações do banco de dados
include_once 'classes/Usuario.php'; // Classe de usuário

// Instancia o objeto de usuário
$usuario = new Usuario($banco);
$erro_senha = '';
$sucesso_senha = '';

// Lógica para alteração de senha (caso o formulário seja enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alterar_senha'])) {
    $email = $_POST['email_senha'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verifica se as senhas coincidem
    if ($nova_senha !== $confirmar_senha) {
        $erro_senha = 'As senhas não coincidem.';
    } else {
        // Busca o usuário pelo email
        $dados_usuario = $usuario->buscarPorEmail($email);
        if ($dados_usuario) {
            // Atualiza a senha do usuário
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_BCRYPT);
            $stmt = $banco->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
            if ($stmt->execute([$nova_senha_hash, $email])) {
                $sucesso_senha = 'Senha alterada com sucesso!';
            } else {
                $erro_senha = 'Erro ao alterar a senha.';
            }
        } else {
            $erro_senha = 'Email não encontrado.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - CSL Times</title>
    <link rel="stylesheet" href="uploads/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="uploads/alterarSenha.css">
    <link rel="icon" href="./assets/img/logo.png" type="image/png">
    <script src="./scripts/alterarSenha.js"></script>
</head>
<body class="portal-body">
  
    <div class="modal active" id="modalSenha">
        <div class="modal-content">
            <span class="close-modal" onclick="window.location.href='index.php'">&times;</span>
            <h2 style="color: black;"><i class="fas fa-key"></i> Alterar Senha</h2>
            <form method="POST" id="formAlterarSenha">
                <div class="form-group">
                    <label for="email_senha">Email</label>
                    <input type="email" name="email_senha" id="email_senha" required placeholder="Digite seu email">
                </div>
                <div class="form-group">
                    <label for="nova_senha">Nova Senha</label>
                    <input type="password" name="nova_senha" id="nova_senha" required placeholder="Digite a nova senha">
                </div>
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Nova Senha</label>
                    <input type="password" name="confirmar_senha" id="confirmar_senha" required placeholder="Confirme a nova senha">
                </div>
                <button type="submit" name="alterar_senha" class="submit-btn">
                    <i class="fas fa-save"></i> Alterar Senha
                </button>
            </form>
            <div class="register-link">
                <p style="color: black;">Lembrou sua senha? <a href="#" onclick="window.location.href='Logar.php'; return false;">Fazer Login</a></p>
            </div>
            <?php if (!empty($erro_senha)): ?>
                <div class="login-error"><?php echo $erro_senha; ?></div>
            <?php endif; ?>
            <?php if (!empty($sucesso_senha)): ?>
                <div class="login-success"><?php echo $sucesso_senha; ?></div>
            <?php endif; ?>
        </div>
    </div>
 
</body>
</html>
