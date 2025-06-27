// Alterna a exibição do menu de moedas no mobile
function alternarMenuMoedas() {
    var ticker = document.getElementById('ticker-content');
    ticker.classList.toggle('active');
}

// Fecha o menu de moedas se clicar fora dele (mobile)
document.addEventListener('click', function(event) {
    var ticker = document.getElementById('ticker-content');
    var hamburger = document.getElementById('hamburger-currency');
    if (window.innerWidth <= 768) {
        if (!ticker.contains(event.target) && event.target !== hamburger) {
            ticker.classList.remove('active');
        }
    }
});

// Exibe ou esconde o rodapé conforme o scroll da página
window.addEventListener('scroll', function() {
    const footer = document.querySelector('.footer-main');
    const scrollPosition = window.scrollY + window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;
    // Se o usuário está próximo do final da página, mostra o rodapé
    if (documentHeight - scrollPosition < 100) {
        footer.style.display = 'block';
    } else {
        footer.style.display = 'none';
    }
});

// Abre o modal de login (caso exista na página)
function abrirModal() {
    document.getElementById('loginModal').classList.add('active');
}

// Fecha o modal de login
function fecharModal() {
    document.getElementById('loginModal').classList.remove('active');
}

// Fecha o modal de login ao clicar fora dele
window.onclick = function(event) {
    const loginModal = document.getElementById('loginModal');
    if (event.target == loginModal) {
        fecharModal();
    }
}

// Fecha o modal de login ao pressionar ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        fecharModal();
    }
});

// Abre o modal de notícia com os dados recebidos do PHP
function abrirModalNoticia(noticia, autor, categoria) {
    document.getElementById('modal-noticia-titulo').textContent = noticia.titulo;
    const data = new Date(noticia.data);
    const dataFormatada = data.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    document.getElementById('modal-noticia-data').textContent = dataFormatada;
    document.getElementById('modal-noticia-autor').textContent = autor;
    document.getElementById('modal-noticia-categoria').textContent = categoria;
    document.getElementById('modal-noticia-conteudo').textContent = noticia.noticia;
    // Configura a imagem, se existir
    const imagemContainer = document.getElementById('modal-noticia-imagem-container');
    const imagem = document.getElementById('modal-noticia-imagem');
    if (noticia.imagem && noticia.imagem.trim() !== '') {
        let imgSrc = noticia.imagem;
        if (imgSrc.startsWith('@')) {
            imgSrc = imgSrc.substring(1);
        }
        if (!imgSrc.startsWith('http')) {
            imgSrc = 'uploads/' + imgSrc;
        }
        imagem.src = imgSrc;
        imagem.alt = noticia.titulo;
        imagemContainer.style.display = 'flex';
    } else {
        imagemContainer.style.display = 'none';
    }
    document.getElementById('noticiaModal').style.display = 'flex';
}

// Fecha o modal de notícia
function fecharModalNoticia() {
    document.getElementById('noticiaModal').style.display = 'none';
}

// Fecha o modal de notícia ao clicar fora dele
window.addEventListener('click', function(event) {
    const noticiaModal = document.getElementById('noticiaModal');
    if (event.target === noticiaModal) {
        fecharModalNoticia();
    }
});
