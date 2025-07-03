document.addEventListener('DOMContentLoaded', function() {
    
    // ====== ELEMENTOS DO FORMULÁRIO ======
    const form = document.querySelector('.news-form');
    const tituloInput = document.getElementById('titulo');
    const conteudoTextarea = document.getElementById('conteudo');
    const categoriaSelect = document.getElementById('categoria_id');
    const imagemInput = document.getElementById('imagem');
    const imagePreview = document.getElementById('image-preview');
    const currentImagePreview = document.getElementById('current-image-preview');

    // ====== PREVIEW DE IMAGEM ======
    
    // Função para visualizar imagem selecionada
    function visualizarImagem(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Valida tipo de arquivo
            if (!file.type.startsWith('image/')) {
                alert('Por favor, selecione apenas arquivos de imagem.');
                input.value = '';
                return;
            }
            
            // Valida tamanho (máximo 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('A imagem deve ter no máximo 5MB.');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                imagePreview.innerHTML = `
                    <div class="new-image-preview">
                        <img src="${e.target.result}" alt="Preview da nova imagem" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                        <button type="button" class="remove-new-image" onclick="removerNovaImagem()">
                            <i class="fas fa-times"></i> Remover
                        </button>
                    </div>
                `;
                
                if (currentImagePreview) {
                    currentImagePreview.style.display = 'none';
                }
            }
            
            reader.readAsDataURL(file);
        } else {
            resetImagePreview();
        }
    }

    // Função para remover nova imagem
    window.removerNovaImagem = function() {
        imagemInput.value = '';
        resetImagePreview();
    }

    // Função para resetar preview
    function resetImagePreview() {
        imagePreview.innerHTML = `
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Clique para selecionar uma nova imagem</p>
        `;
        
        if (currentImagePreview) {
            currentImagePreview.style.display = 'block';
        }
    }

    // Adiciona listener para o input de imagem
    imagemInput.addEventListener('change', function() {
        visualizarImagem(this);
    });

    // ====== CONTADORES DE CARACTERES ======
    
    // Cria contadores se não existirem
    function createCharacterCounters() {
        // Contador para título
        if (!document.getElementById('titulo-counter')) {
            const tituloCounter = document.createElement('div');
            tituloCounter.id = 'titulo-counter';
            tituloCounter.className = 'char-counter';
            tituloCounter.style.cssText = 'font-size: 0.8rem; color: #666; text-align: right; margin-top: 5px;';
            tituloInput.parentElement.appendChild(tituloCounter);
        }
        
        // Contador para conteúdo
        if (!document.getElementById('conteudo-counter')) {
            const conteudoCounter = document.createElement('div');
            conteudoCounter.id = 'conteudo-counter';
            conteudoCounter.className = 'char-counter';
            conteudoCounter.style.cssText = 'font-size: 0.8rem; color: #666; text-align: right; margin-top: 5px;';
            conteudoTextarea.parentElement.appendChild(conteudoCounter);
        }
    }

    // Atualiza contadores
    function updateCharacterCounters() {
        const tituloCounter = document.getElementById('titulo-counter');
        const conteudoCounter = document.getElementById('conteudo-counter');
        
        if (tituloCounter) {
            const tituloLength = tituloInput.value.length;
            tituloCounter.textContent = `${tituloLength}/100 caracteres`;
            tituloCounter.style.color = tituloLength > 80 ? '#e74c3c' : '#666';
        }
        
        if (conteudoCounter) {
            const conteudoLength = conteudoTextarea.value.length;
            conteudoCounter.textContent = `${conteudoLength}/2000 caracteres`;
            conteudoCounter.style.color = conteudoLength > 1800 ? '#e74c3c' : '#666';
        }
    }

    // Inicializa contadores
    createCharacterCounters();
    updateCharacterCounters();

    // Listeners para atualizar contadores
    tituloInput.addEventListener('input', updateCharacterCounters);
    conteudoTextarea.addEventListener('input', updateCharacterCounters);

    // ====== VALIDAÇÃO DO FORMULÁRIO ======
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessage = '';

        // ====== VALIDAÇÃO DO TÍTULO ======
        if (tituloInput.value.trim().length < 5) {
            isValid = false;
            errorMessage += 'Título deve ter pelo menos 5 caracteres.\n';
            tituloInput.style.borderColor = '#ff4444';
        } else if (tituloInput.value.trim().length > 100) {
            isValid = false;
            errorMessage += 'Título deve ter no máximo 100 caracteres.\n';
            tituloInput.style.borderColor = '#ff4444';
        } else {
            tituloInput.style.borderColor = '';
        }

        // ====== VALIDAÇÃO DO CONTEÚDO ======
        if (conteudoTextarea.value.trim().length < 50) {
            isValid = false;
            errorMessage += 'Conteúdo deve ter pelo menos 50 caracteres.\n';
            conteudoTextarea.style.borderColor = '#ff4444';
        } else if (conteudoTextarea.value.trim().length > 2000) {
            isValid = false;
            errorMessage += 'Conteúdo deve ter no máximo 2000 caracteres.\n';
            conteudoTextarea.style.borderColor = '#ff4444';
        } else {
            conteudoTextarea.style.borderColor = '';
        }

        // ====== VALIDAÇÃO DA CATEGORIA ======
        if (categoriaSelect.value === '') {
            isValid = false;
            errorMessage += 'Selecione uma categoria.\n';
            categoriaSelect.style.borderColor = '#ff4444';
        } else {
            categoriaSelect.style.borderColor = '';
        }

        // Se houver erros, impede o envio e exibe mensagem
        if (!isValid) {
            e.preventDefault();
            alert('Por favor, corrija os seguintes erros:\n' + errorMessage);
        } else {
            // Confirmação antes de enviar
            if (!confirm('Tem certeza que deseja salvar as alterações da notícia?')) {
                e.preventDefault();
            }
        }
    });

    // ====== VALIDAÇÃO EM TEMPO REAL ======
    
    // Validação do título
    tituloInput.addEventListener('blur', function() {
        if (this.value.trim().length < 5) {
            this.style.borderColor = '#ff4444';
            this.title = 'Título deve ter pelo menos 5 caracteres';
        } else if (this.value.trim().length > 100) {
            this.style.borderColor = '#ff4444';
            this.title = 'Título deve ter no máximo 100 caracteres';
        } else {
            this.style.borderColor = '';
            this.title = '';
        }
    });

    // Validação do conteúdo
    conteudoTextarea.addEventListener('blur', function() {
        if (this.value.trim().length < 50) {
            this.style.borderColor = '#ff4444';
            this.title = 'Conteúdo deve ter pelo menos 50 caracteres';
        } else if (this.value.trim().length > 2000) {
            this.style.borderColor = '#ff4444';
            this.title = 'Conteúdo deve ter no máximo 2000 caracteres';
        } else {
            this.style.borderColor = '';
            this.title = '';
        }
    });

    // ====== FEEDBACK VISUAL ======
    
    // Função para adicionar feedback visual positivo aos campos
    function addValidFeedback(element) {
        element.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.style.borderColor = '#28a745';
            } else {
                this.style.borderColor = '';
            }
        });
    }

    // Aplica feedback visual aos campos principais
    addValidFeedback(tituloInput);
    addValidFeedback(conteudoTextarea);

    // ====== NAVEGAÇÃO POR TECLADO ======
    
    // Previne envio do formulário quando Enter é pressionado em campos de texto
    const textInputs = document.querySelectorAll('input[type="text"], textarea');
    textInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.tagName !== 'TEXTAREA') {
                e.preventDefault();
                
                // Foca no próximo campo
                const nextInput = this.parentElement.nextElementSibling?.querySelector('input, textarea, select');
                if (nextInput) {
                    nextInput.focus();
                } else {
                    document.querySelector('.btn-primary').focus();
                }
            }
        });
    });

    // ====== ATALHOS DE TECLADO ======
    
    // Atalho ESC para sair da página com confirmação
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (confirm('Deseja sair sem salvar as alterações?')) {
                window.location.href = 'portal.php';
            }
        }
    });

    // ====== PROTEÇÃO CONTRA PERDA DE DADOS ======
    
    // Botões de voltar com confirmação se houver alterações
    const voltarBtns = document.querySelectorAll('a[href="portal.php"]');
    voltarBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (formHasChanges()) {
                if (!confirm('Deseja sair sem salvar as alterações?')) {
                    e.preventDefault();
                }
            }
        });
    });

    // ====== FUNÇÕES AUXILIARES ======
    
    /**
     * Verifica se o formulário foi alterado comparando valores atuais com originais
     * @returns {boolean} True se houve alterações, False caso contrário
     */
    function formHasChanges() {
        const originalValues = {
            titulo: tituloInput.defaultValue,
            conteudo: conteudoTextarea.defaultValue,
            categoria: categoriaSelect.defaultValue,
            imagem: imagemInput.value
        };

        const currentValues = {
            titulo: tituloInput.value,
            conteudo: conteudoTextarea.value,
            categoria: categoriaSelect.value,
            imagem: imagemInput.value
        };

        return JSON.stringify(originalValues) !== JSON.stringify(currentValues);
    }

    // ====== INICIALIZAÇÃO ======
    
    // Salva os valores originais para comparação posterior
    tituloInput.defaultValue = tituloInput.value;
    conteudoTextarea.defaultValue = conteudoTextarea.value;
    categoriaSelect.defaultValue = categoriaSelect.value;

    // ====== AUTO-SAVE (OPCIONAL) ======
    
    // Salva dados no localStorage a cada 30 segundos
    setInterval(function() {
        if (formHasChanges()) {
            const formData = {
                titulo: tituloInput.value,
                conteudo: conteudoTextarea.value,
                categoria: categoriaSelect.value
            };
            localStorage.setItem('noticiaEditDraft', JSON.stringify(formData));
        }
    }, 30000);

    // Recupera dados salvos ao carregar a página
    const savedData = localStorage.getItem('noticiaEditDraft');
    if (savedData && !formHasChanges()) {
        const data = JSON.parse(savedData);
        tituloInput.value = data.titulo || tituloInput.value;
        conteudoTextarea.value = data.conteudo || conteudoTextarea.value;
        categoriaSelect.value = data.categoria || categoriaSelect.value;
        updateCharacterCounters();
    }

    // Limpa dados salvos após envio bem-sucedido
    form.addEventListener('submit', function() {
        localStorage.removeItem('noticiaEditDraft');
    });

    // ====== MELHORIAS NA INTERFACE ======
    
    // Adiciona classe para estilização do file upload
    const imagePreviewContainer = document.querySelector('.image-preview');
    if (imagePreviewContainer) {
        imagePreviewContainer.addEventListener('click', function() {
            imagemInput.click();
        });
    }

    // ====== MENSAGENS DE ERRO ======
    
    // Auto-oculta mensagens de erro após 8 segundos
    const errorMessage = document.querySelector('.error-message');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 500);
        }, 8000);
    }

    // ====== ESTILOS DINÂMICOS ======
    
    // Adiciona estilos para nova imagem preview
    const style = document.createElement('style');
    style.textContent = `
        .new-image-preview {
            text-align: center;
        }
        
        .new-image-preview img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }
        
        .new-image-preview img:hover {
            transform: scale(1.05);
        }
        
        .remove-new-image {
            margin-top: 10px;
            padding: 8px 16px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .remove-new-image:hover {
            background: #c0392b;
            transform: translateY(-1px);
        }
    `;
    document.head.appendChild(style);

    // ====== DARK MODE FUNCTIONALITY ======
    const btn = document.getElementById('toggle-theme');
    
    if (btn) {
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
    }
});
