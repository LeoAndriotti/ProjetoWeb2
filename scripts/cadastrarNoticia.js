document.addEventListener('DOMContentLoaded', function() {
    
    // ====== ELEMENTOS DO FORMULÁRIO ======
    const form = document.querySelector('.portal-form');
    const tituloInput = document.getElementById('titulo');
    const noticiaTextarea = document.getElementById('noticia');
    const dataInput = document.getElementById('data');
    const autorSelect = document.getElementById('autor');
    const categoriaSelect = document.getElementById('categoria');
    const imagemInput = document.getElementById('imagem');
    const imagemUrlInput = document.getElementById('imagem_url');
    const fileUploadLabel = document.querySelector('.file-upload-label');

    // ====== PREVIEW DE IMAGEM ======
    let imagePreview = null;

    // Cria elemento de preview se não existir
    function createImagePreview() {
        if (!imagePreview) {
            imagePreview = document.createElement('div');
            imagePreview.className = 'image-preview-container';
            imagePreview.innerHTML = `
                <div class="image-preview">
                    <img id="preview-img" src="" alt="Preview da imagem" style="display: none; max-width: 200px; max-height: 150px; border-radius: 8px; margin-top: 10px;">
                    <button type="button" id="remove-image" class="remove-image-btn" style="display: none; margin-top: 10px; padding: 5px 10px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        <i class="fas fa-times"></i> Remover Imagem
                    </button>
                </div>
            `;
            
            // Insere após o campo de URL
            imagemUrlInput.parentElement.appendChild(imagePreview);
            
            // Adiciona listener para remover imagem
            document.getElementById('remove-image').addEventListener('click', removeImage);
        }
    }

    // Função para mostrar preview da imagem
    function showImagePreview(src) {
        createImagePreview();
        const previewImg = document.getElementById('preview-img');
        const removeBtn = document.getElementById('remove-image');
        
        previewImg.src = src;
        previewImg.style.display = 'block';
        removeBtn.style.display = 'block';
    }

    // Função para remover imagem
    function removeImage() {
        const previewImg = document.getElementById('preview-img');
        const removeBtn = document.getElementById('remove-image');
        
        previewImg.src = '';
        previewImg.style.display = 'none';
        removeBtn.style.display = 'none';
        
        // Limpa os campos
        imagemInput.value = '';
        imagemUrlInput.value = '';
    }

    // ====== HANDLERS DE IMAGEM ======
    
    // Preview de arquivo selecionado
    imagemInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Valida tipo de arquivo
            if (!file.type.startsWith('image/')) {
                alert('Por favor, selecione apenas arquivos de imagem.');
                this.value = '';
                return;
            }
            
            // Valida tamanho (máximo 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('A imagem deve ter no máximo 5MB.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                showImagePreview(e.target.result);
            };
            reader.readAsDataURL(file);
            
            // Limpa URL se houver
            imagemUrlInput.value = '';
        }
    });

    // Preview de URL de imagem
    imagemUrlInput.addEventListener('blur', function() {
        const url = this.value.trim();
        if (url) {
            // Valida se é uma URL válida
            try {
                new URL(url);
                showImagePreview(url);
            } catch (e) {
                alert('Por favor, insira uma URL válida.');
                this.value = '';
            }
        }
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
        
        // Contador para notícia
        if (!document.getElementById('noticia-counter')) {
            const noticiaCounter = document.createElement('div');
            noticiaCounter.id = 'noticia-counter';
            noticiaCounter.className = 'char-counter';
            noticiaCounter.style.cssText = 'font-size: 0.8rem; color: #666; text-align: right; margin-top: 5px;';
            noticiaTextarea.parentElement.appendChild(noticiaCounter);
        }
    }

    // Atualiza contadores
    function updateCharacterCounters() {
        const tituloCounter = document.getElementById('titulo-counter');
        const noticiaCounter = document.getElementById('noticia-counter');
        
        if (tituloCounter) {
            const tituloLength = tituloInput.value.length;
            tituloCounter.textContent = `${tituloLength}/100 caracteres`;
            tituloCounter.style.color = tituloLength > 80 ? '#e74c3c' : '#666';
        }
        
        if (noticiaCounter) {
            const noticiaLength = noticiaTextarea.value.length;
            noticiaCounter.textContent = `${noticiaLength}/2000 caracteres`;
            noticiaCounter.style.color = noticiaLength > 1800 ? '#e74c3c' : '#666';
        }
    }

    // Inicializa contadores
    createCharacterCounters();
    updateCharacterCounters();

    // Listeners para atualizar contadores
    tituloInput.addEventListener('input', updateCharacterCounters);
    noticiaTextarea.addEventListener('input', updateCharacterCounters);

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
        if (noticiaTextarea.value.trim().length < 50) {
            isValid = false;
            errorMessage += 'Conteúdo deve ter pelo menos 50 caracteres.\n';
            noticiaTextarea.style.borderColor = '#ff4444';
        } else if (noticiaTextarea.value.trim().length > 2000) {
            isValid = false;
            errorMessage += 'Conteúdo deve ter no máximo 2000 caracteres.\n';
            noticiaTextarea.style.borderColor = '#ff4444';
        } else {
            noticiaTextarea.style.borderColor = '';
        }

        // ====== VALIDAÇÃO DA DATA ======
        const selectedDate = new Date(dataInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate > today) {
            isValid = false;
            errorMessage += 'A data não pode ser futura.\n';
            dataInput.style.borderColor = '#ff4444';
        } else {
            dataInput.style.borderColor = '';
        }

        // ====== VALIDAÇÃO DO AUTOR ======
        if (autorSelect.value === '') {
            isValid = false;
            errorMessage += 'Selecione um autor.\n';
            autorSelect.style.borderColor = '#ff4444';
        } else {
            autorSelect.style.borderColor = '';
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
            if (!confirm('Tem certeza que deseja publicar esta notícia?')) {
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
    noticiaTextarea.addEventListener('blur', function() {
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

    // Validação da data
    dataInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate > today) {
            this.style.borderColor = '#ff4444';
            this.title = 'A data não pode ser futura';
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
    addValidFeedback(noticiaTextarea);

    // ====== NAVEGAÇÃO POR TECLADO ======
    
    // Previne envio do formulário quando Enter é pressionado em campos de texto
    const textInputs = document.querySelectorAll('input[type="text"], input[type="date"], input[type="url"], textarea');
    textInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.tagName !== 'TEXTAREA') {
                e.preventDefault();
                
                // Foca no próximo campo
                const nextInput = this.parentElement.nextElementSibling?.querySelector('input, textarea, select');
                if (nextInput) {
                    nextInput.focus();
                } else {
                    document.querySelector('.submit-btn').focus();
                }
            }
        });
    });

    // ====== ATALHOS DE TECLADO ======
    
    // Atalho ESC para sair da página com confirmação
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (confirm('Deseja sair sem publicar a notícia?')) {
                window.location.href = 'portal.php';
            }
        }
    });

    // ====== PROTEÇÃO CONTRA PERDA DE DADOS ======
    
    // Botão de voltar com confirmação se houver dados preenchidos
    const voltarBtn = document.querySelector('a[href="portal.php"]');
    if (voltarBtn) {
        voltarBtn.addEventListener('click', function(e) {
            if (formHasData()) {
                if (!confirm('Deseja sair sem publicar a notícia?')) {
                    e.preventDefault();
                }
            }
        });
    }

    // ====== FUNÇÕES AUXILIARES ======
    
    /**
     * Verifica se o formulário tem dados preenchidos
     * @returns {boolean} True se há dados, False caso contrário
     */
    function formHasData() {
        return tituloInput.value.trim() !== '' ||
               noticiaTextarea.value.trim() !== '' ||
               dataInput.value !== '' ||
               autorSelect.value !== '' ||
               categoriaSelect.value !== '' ||
               imagemInput.value !== '' ||
               imagemUrlInput.value.trim() !== '';
    }

    // ====== CONFIGURAÇÃO INICIAL ======
    
    // Define data atual como padrão
    const today = new Date().toISOString().split('T')[0];
    dataInput.value = today;
    
    // Adiciona classe para estilização do file upload
    if (fileUploadLabel) {
        fileUploadLabel.addEventListener('click', function() {
            imagemInput.click();
        });
    }

    // ====== AUTO-SAVE (OPCIONAL) ======
    
    // Salva dados no localStorage a cada 30 segundos
    setInterval(function() {
        if (formHasData()) {
            const formData = {
                titulo: tituloInput.value,
                noticia: noticiaTextarea.value,
                data: dataInput.value,
                autor: autorSelect.value,
                categoria: categoriaSelect.value,
                imagemUrl: imagemUrlInput.value
            };
            localStorage.setItem('noticiaDraft', JSON.stringify(formData));
        }
    }, 30000);

    // Recupera dados salvos ao carregar a página
    const savedData = localStorage.getItem('noticiaDraft');
    if (savedData && !formHasData()) {
        const data = JSON.parse(savedData);
        tituloInput.value = data.titulo || '';
        noticiaTextarea.value = data.noticia || '';
        dataInput.value = data.data || today;
        autorSelect.value = data.autor || '';
        categoriaSelect.value = data.categoria || '';
        imagemUrlInput.value = data.imagemUrl || '';
        updateCharacterCounters();
    }

    // Limpa dados salvos após envio bem-sucedido
    form.addEventListener('submit', function() {
        localStorage.removeItem('noticiaDraft');
    });
});
