// Script de interações para a página de alteração de senha

// Seleciona o modal de alteração de senha pelo ID
const alterarSenhaModal = document.getElementById('alterarSenhaModal');

// Função para fechar o modal de alteração de senha e redirecionar para a página inicial
function fecharModalAlterarSenha() {
    window.location.href = 'index.php';
}

// Fecha o modal ao pressionar a tecla ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        fecharModalAlterarSenha();
    }
});

// Fecha o modal ao clicar fora do conteúdo do modal
document.addEventListener('click', function(event) {
    if (event.target === alterarSenhaModal) {
        fecharModalAlterarSenha();
    }
});

// Outras interações JS específicas da alteração de senha podem ser adicionadas aqui 