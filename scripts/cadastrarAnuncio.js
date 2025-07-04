// ====== SCRIPT PARA CADASTRAR ANÚNCIO ======

document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de cadastrar anúncio carregado');
    
    // ====== DARK MODE TOGGLE ======
    const themeToggle = document.getElementById('toggle-theme');
    const body = document.body;
    
    // Verificar se há preferência salva
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
    }
    
    themeToggle.addEventListener('click', function() {
        body.classList.toggle('dark-mode');
        
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
        } else {
            localStorage.setItem('theme', 'light');
            themeToggle.innerHTML = '<i class="fa-solid fa-moon"></i>';
        }
    });
    
    // ====== UPLOAD DE ARQUIVO ======
    const fileInput = document.getElementById('imagem');
    const fileLabel = document.querySelector('.file-upload-label');
    const urlInput = document.getElementById('imagem_url');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tipo de arquivo
            if (!file.type.startsWith('image/')) {
                alert('Por favor, selecione apenas arquivos de imagem.');
                fileInput.value = '';
                return;
            }
            
            // Validar tamanho (máximo 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('O arquivo é muito grande. Tamanho máximo: 5MB');
                fileInput.value = '';
                return;
            }
            
            // Atualizar label
            fileLabel.innerHTML = `<i class="fas fa-check"></i><span>${file.name}</span>`;
            fileLabel.style.borderColor = '#28a745';
            fileLabel.style.background = 'rgba(40, 167, 69, 0.1)';
            
            // Limpar URL se arquivo foi selecionado
            urlInput.value = '';
        }
    });
    
    // ====== VALIDAÇÃO DE URL ======
    urlInput.addEventListener('input', function() {
        if (this.value.trim()) {
            // Se URL foi inserida, limpar arquivo
            fileInput.value = '';
            fileLabel.innerHTML = '<i class="fas fa-cloud-upload-alt"></i><span>Escolher arquivo</span>';
            fileLabel.style.borderColor = 'rgba(102, 126, 234, 0.3)';
            fileLabel.style.background = 'rgba(102, 126, 234, 0.05)';
        }
    });
    
    // ====== VALIDAÇÃO DO FORMULÁRIO ======
    const form = document.querySelector('.portal-form');
    
    form.addEventListener('submit', function(e) {
        const titulo = document.getElementById('titulo').value.trim();
        const noticia = document.getElementById('noticia').value.trim();
        const data = document.getElementById('data').value;
        const categoria = document.getElementById('categoria').value;
        const imagem = fileInput.files[0];
        const imagemUrl = urlInput.value.trim();
        
        // Validar campos obrigatórios
        if (!titulo) {
            e.preventDefault();
            alert('Por favor, preencha o título do anúncio.');
            document.getElementById('titulo').focus();
            return;
        }
        
        if (!noticia) {
            e.preventDefault();
            alert('Por favor, preencha o conteúdo do anúncio.');
            document.getElementById('noticia').focus();
            return;
        }
        
        if (!data) {
            e.preventDefault();
            alert('Por favor, selecione uma data.');
            document.getElementById('data').focus();
            return;
        }
        
        if (!categoria) {
            e.preventDefault();
            alert('Por favor, selecione uma categoria.');
            document.getElementById('categoria').focus();
            return;
        }
        
        // Validar se pelo menos uma imagem foi fornecida
        if (!imagem && !imagemUrl) {
            e.preventDefault();
            alert('Por favor, forneça uma imagem (upload ou URL).');
            return;
        }
        
        // Validar URL se fornecida
        if (imagemUrl && !isValidUrl(imagemUrl)) {
            e.preventDefault();
            alert('Por favor, forneça uma URL válida para a imagem.');
            urlInput.focus();
            return;
        }
        
        // Se tudo estiver ok, mostrar loading
        const submitBtn = document.querySelector('.submit-btn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publicando...';
        submitBtn.disabled = true;
    });
    
    // ====== FUNÇÃO PARA VALIDAR URL ======
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
    
    // ====== ANIMAÇÕES ======
    const formCard = document.querySelector('.form-card');
    
    // Adicionar animação de entrada
    formCard.style.opacity = '0';
    formCard.style.transform = 'translateY(30px)';
    
    setTimeout(() => {
        formCard.style.transition = 'all 0.6s ease-out';
        formCard.style.opacity = '1';
        formCard.style.transform = 'translateY(0)';
    }, 100);
    
    // ====== MÁSCARA PARA CAMPO DE VALOR ======
    const valorInput = document.getElementById('valorAnuncio');
    
    if (valorInput) {
        function formatarMoeda(valor) {
            valor = valor.replace(/\D/g, '');
            if (valor === '') return '';
            while (valor.length < 3) valor = '0' + valor; // Garante pelo menos 3 dígitos
            let reais = valor.slice(0, -2);
            let centavos = valor.slice(-2);
            reais = reais.replace(/^0+/, '') || '0';
            const reaisFormatados = reais.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return `R$ ${reaisFormatados},${centavos}`;
        }

        valorInput.addEventListener('input', function(e) {
            const cursor = valorInput.selectionStart;
            const valorFormatado = formatarMoeda(e.target.value);
            e.target.value = valorFormatado;
        });

        valorInput.addEventListener('focus', function() {
            if (this.value === '') {
                this.value = 'R$ 0,00';
            } else {
                this.value = formatarMoeda(this.value);
            }
        });

        valorInput.addEventListener('blur', function() {
            if (this.value === 'R$ 0,00' || this.value === 'R$ ') {
                this.value = '';
            } else {
                this.value = formatarMoeda(this.value);
            }
        });

        valorInput.addEventListener('paste', function(e) {
            e.preventDefault();
            let texto = (e.clipboardData || window.clipboardData).getData('text');
            this.value = formatarMoeda(texto);
        });

        valorInput.addEventListener('keydown', function(e) {
            // Permitir: backspace, delete, tab, escape, enter, setas
            const allowedKeys = [8, 9, 27, 13, 37, 38, 39, 40, 46];
            // Permitir números (0-9)
            if ((e.keyCode >= 48 && e.keyCode <= 57) || 
                (e.keyCode >= 96 && e.keyCode <= 105) || 
                allowedKeys.includes(e.keyCode)) {
                return;
            }
            // Permitir Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            if (e.ctrlKey && (e.keyCode === 65 || e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 88)) {
                return;
            }
            e.preventDefault();
        });
    }
    
    // ====== MELHORAR UX DOS CAMPOS ======
    const inputs = document.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        // Adicionar classe quando focado
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
        
        // Auto-resize para textarea
        if (input.tagName === 'TEXTAREA') {
            input.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }
    });
    
    // ====== PREVIEW DE IMAGEM (se implementado) ======
    // Aqui você pode adicionar funcionalidade de preview de imagem
    // quando um arquivo for selecionado ou uma URL for inserida
    
    console.log('Script de cadastrar anúncio inicializado com sucesso');
});
