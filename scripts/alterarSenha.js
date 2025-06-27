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