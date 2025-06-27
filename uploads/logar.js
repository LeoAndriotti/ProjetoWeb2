// Script de interações para a página de login

// Seleciona o modal de login pelo ID
const loginModal = document.getElementById('loginModal');

// Função para fechar o modal de login e redirecionar para a página inicial
function fecharModalLogin() {
    window.location.href = 'index.php';
}

// Fecha o modal ao pressionar a tecla ESC
// (Já existe no HTML, mas pode ser útil para reforçar)
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        fecharModalLogin();
    }
});

// Fecha o modal ao clicar fora do conteúdo do modal
// (Já existe no HTML, mas pode ser útil para reforçar)
document.addEventListener('click', function(event) {
    if (event.target === loginModal) {
        fecharModalLogin();
    }
});

// Você pode adicionar outras interações JS específicas do login aqui, se necessário 