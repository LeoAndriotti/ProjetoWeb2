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

function abrirNoticiaIndex(){
    alert("Cadastre-se para ler a noticia completa!")
}

// Abre o modal de notícia (usado em páginas de usuário logado)
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

// Exibe um alerta ao clicar em um card de notícia (para visitantes)
function noticiaCardClickAlert() {
    alert('Faça login ou cadastre-se para ver a notícia completa!');
}

// Dark mode functionality - wrapped in DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('toggle-theme');
    
    if (!btn) {
        console.error('Botão de tema não encontrado');
        return;
    }
    
    function updateThemeBtn() {
        if (document.body.classList.contains('dark-mode')) {
            btn.innerHTML = '<i class="fa-solid fa-sun"></i>';
        } else {
            btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
        }
    }
    
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme');
    
    if (savedTheme) {
        document.body.classList.toggle('dark-mode', savedTheme === 'dark');
        document.body.classList.toggle('light-mode', savedTheme === 'light');
    } else if (prefersDark) {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.add('light-mode');
    }
    
    updateThemeBtn();
    
    btn.onclick = function() {
        document.body.classList.toggle('dark-mode');
        document.body.classList.toggle('light-mode');
        const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        updateThemeBtn();
    };
});