// ====== SCRIPT PARA ALTERAR ANÚNCIO ======

document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de alterar anúncio carregado');
    
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
    
    // ====== MÁSCARA PARA CAMPO DE VALOR ======
    const valorInput = document.getElementById('valorAnuncio');
    
    if (valorInput) {
        function formatarMoeda(valor) {
            // Remove tudo que não é número
            valor = valor.replace(/\D/g, '');
            
            // Se não há valor, retorna vazio
            if (valor === '') return '';
            
            // Garante pelo menos 3 dígitos (para centavos)
            while (valor.length < 3) valor = '0' + valor;
            
            // Separa reais e centavos
            let reais = valor.slice(0, -2);
            let centavos = valor.slice(-2);
            
            // Remove zeros à esquerda dos reais, mas mantém pelo menos um zero
            reais = reais.replace(/^0+/, '') || '0';
            
            // Adiciona pontos para milhares
            const reaisFormatados = reais.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            
            return `R$ ${reaisFormatados},${centavos}`;
        }

        // Função para obter apenas números do valor formatado
        function obterNumeros(valor) {
            return valor.replace(/\D/g, '');
        }

        // Função para posicionar o cursor corretamente
        function posicionarCursor(input, valorAntigo, valorNovo) {
            const cursorAntigo = input.selectionStart;
            const numerosAntigos = obterNumeros(valorAntigo);
            const numerosNovos = obterNumeros(valorNovo);
            
            // Calcula a diferença de caracteres não numéricos
            const diffNaoNumericos = valorNovo.length - numerosNovos.length - (valorAntigo.length - numerosAntigos.length);
            
            // Ajusta a posição do cursor
            let novaPosicao = cursorAntigo + diffNaoNumericos;
            
            // Garante que a posição está dentro dos limites
            novaPosicao = Math.max(0, Math.min(novaPosicao, valorNovo.length));
            
            input.setSelectionRange(novaPosicao, novaPosicao);
        }

        valorInput.addEventListener('input', function(e) {
            const valorAntigo = this.value;
            const valorFormatado = formatarMoeda(e.target.value);
            
            if (valorFormatado !== valorAntigo) {
                this.value = valorFormatado;
                posicionarCursor(this, valorAntigo, valorFormatado);
            }
        });

        valorInput.addEventListener('focus', function() {
            if (this.value === '') {
                this.value = 'R$ 0,00';
            } else if (!this.value.startsWith('R$')) {
                this.value = formatarMoeda(this.value);
            }
        });

        valorInput.addEventListener('blur', function() {
            if (this.value === 'R$ 0,00' || this.value === 'R$ ') {
                this.value = '';
            } else if (this.value && !this.value.startsWith('R$')) {
                this.value = formatarMoeda(this.value);
            }
        });

        valorInput.addEventListener('paste', function(e) {
            e.preventDefault();
            let texto = (e.clipboardData || window.clipboardData).getData('text');
            this.value = formatarMoeda(texto);
        });

        valorInput.addEventListener('keydown', function(e) {
            // Permitir: backspace, delete, tab, escape, enter, setas, home, end
            const teclasPermitidas = [8, 9, 27, 13, 37, 38, 39, 40, 35, 36, 46];
            
            // Permitir números (0-9)
            if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) {
                return true;
            }
            
            // Permitir teclas especiais
            if (teclasPermitidas.includes(e.keyCode)) {
                return true;
            }
            
            // Permitir Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            if (e.ctrlKey && (e.keyCode === 65 || e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 88)) {
                return true;
            }
            
            // Bloquear todas as outras teclas
            e.preventDefault();
            return false;
        });
    }
    
    // ====== VALIDAÇÃO DO FORMULÁRIO ======
    const form = document.querySelector('.portal-form');
    
    form.addEventListener('submit', function(e) {
        const nome = document.getElementById('nome').value.trim();
        const imagem = document.getElementById('imagem').value.trim();
        const link = document.getElementById('link').value.trim();
        const texto = document.getElementById('texto').value.trim();
        const valorAnuncio = document.getElementById('valorAnuncio').value.trim();
        
        // Validar campos obrigatórios
        if (!nome) {
            e.preventDefault();
            alert('Por favor, preencha o nome do anunciante.');
            document.getElementById('nome').focus();
            return;
        }
        
        if (!imagem) {
            e.preventDefault();
            alert('Por favor, preencha a URL da imagem.');
            document.getElementById('imagem').focus();
            return;
        }
        
        if (!link) {
            e.preventDefault();
            alert('Por favor, preencha o link de destino.');
            document.getElementById('link').focus();
            return;
        }
        
        if (!texto) {
            e.preventDefault();
            alert('Por favor, preencha a mensagem/slogan.');
            document.getElementById('texto').focus();
            return;
        }
        
        // Validar URL da imagem
        if (imagem && !isValidUrl(imagem)) {
            e.preventDefault();
            alert('Por favor, forneça uma URL válida para a imagem.');
            document.getElementById('imagem').focus();
            return;
        }
        
        // Validar URL do link
        if (link && !isValidUrl(link)) {
            e.preventDefault();
            alert('Por favor, forneça uma URL válida para o link de destino.');
            document.getElementById('link').focus();
            return;
        }
        
        // Se tudo estiver ok, mostrar loading
        const submitBtn = document.querySelector('.submit-btn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
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
    
    console.log('Script de alterar anúncio inicializado com sucesso');
});
