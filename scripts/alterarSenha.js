function abrirModalSenha() {
    document.getElementById('modalSenha').classList.add('active');
}

function fecharModalSenha() {
    document.getElementById('modalSenha').classList.remove('active');
}

window.onclick = function(event) {
    const senhaModal = document.getElementById('modalSenha');
  
    if (event.target == senhaModal) {
        fecharModalSenha();
    }
}

// Fecha o modal ao pressionar ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            window.location.href = 'index.php';
        }
    });
    // Fecha o modal ao clicar fora
    document.addEventListener('click', function(event) {
        var modal = document.getElementById('modalSenha');
        if (event.target === modal) {
            window.location.href = 'index.php';
        }
    });