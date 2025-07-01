  // Fecha o modal ao pressionar ESC
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        window.location.href = 'index.php';
    }
});
// Fecha o modal ao clicar fora
document.addEventListener('click', function(event) {
    var modal = document.getElementById('loginModal');
    if (event.target === modal) {
        window.location.href = 'index.php';
    }
});

function abrirModal() {
    document.getElementById('loginModal').classList.add('active');
}

function fecharModal() {
    document.getElementById('loginModal').classList.remove('active');
}
function abrirModalSenha() {
    document.getElementById('modalSenha').classList.add('active');
}

function fecharModalSenha() {
    document.getElementById('modalSenha').classList.remove('active');
}

window.onclick = function(event) {
    const loginModal = document.getElementById('loginModal');
    
    if (event.target == loginModal) {
        fecharModal();
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
        var modal = document.getElementById('loginModal');
        if (event.target === modal) {
            window.location.href = 'index.php';
        }
    });