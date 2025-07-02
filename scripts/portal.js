document.addEventListener('DOMContentLoaded', function() {
    
    // ====== ELEMENTOS DO DOM ======
    const modal = document.getElementById('modalNoticia');
    const modalContent = document.getElementById('modalNoticiaContent');
    const newsCards = document.querySelectorAll('.news-card');
    const editButtons = document.querySelectorAll('.news-edit-btn');
    const deleteButtons = document.querySelectorAll('.news-delete-btn');
    const addButton = document.querySelector('.portal-add-btn');

    // ====== FUNÇÕES DO MODAL ======
    
    /**
     * Abre o modal com a notícia selecionada
     * @param {Object} noticia - Objeto com dados da notícia
     */
    window.abrirModalNoticia = function(noticia) {
        if (!modal || !modalContent) return;
        
        // Formatar a data
        const data = new Date(noticia.data);
        const dataFormatada = data.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        // Determinar a URL da imagem
        let imgSrc = '';
        if (noticia.imagem) {
            const img = noticia.imagem.startsWith('@') ? noticia.imagem.substring(1) : noticia.imagem;
            if (img.startsWith('http')) {
                imgSrc = img;
            } else {
                imgSrc = 'uploads/' + img;
            }
        }
        
        // Construir HTML do modal
        let html = `
            <div class="modal-noticia-header">
                <h2>${escapeHtml(noticia.titulo)}</h2>
                <div class="modal-noticia-meta">
                    <span class="modal-noticia-date">
                        <i class="fas fa-calendar"></i> ${dataFormatada}
                    </span>
                    <span class="modal-noticia-category">
                        <i class="fas fa-tag"></i> ${escapeHtml(noticia.categoria_nome || 'Sem categoria')}
                    </span>
                </div>
            </div>
        `;
        
        if (imgSrc) {
            html += `
                <div class="modal-noticia-image">
                    <img src="${imgSrc}" alt="${escapeHtml(noticia.titulo)}" onerror="this.style.display='none'">
                </div>
            `;
        }
        
        html += `
            <div class="modal-noticia-body">
                <p>${escapeHtml(noticia.noticia)}</p>
            </div>
            <div class="modal-noticia-actions">
                <a href="alterarNoticia.php?id=${noticia.id}" class="modal-edit-btn">
                    <i class="fas fa-edit"></i> Editar Notícia
                </a>
                <button onclick="confirmarExclusao(${noticia.id}, '${escapeHtml(noticia.titulo)}')" class="modal-delete-btn">
                    <i class="fas fa-trash-alt"></i> Excluir Notícia
                </button>
            </div>
        `;
        
        modalContent.innerHTML = html;
        
        // Animar abertura do modal
        modal.style.display = 'flex';
        modal.style.opacity = '0';
        
        setTimeout(() => {
            modal.style.opacity = '1';
            modalContent.style.transform = 'scale(1)';
        }, 10);
        
        // Focar no modal para acessibilidade
        modalContent.focus();
        
        // Prevenir scroll do body
        document.body.style.overflow = 'hidden';
    }

    /**
     * Fecha o modal com animação
     */
    window.fecharModalNoticia = function() {
        if (!modal) return;
        
        // Animar fechamento
        modal.style.opacity = '0';
        modalContent.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }

    // ====== CONFIRMAÇÃO DE EXCLUSÃO ======
    
    /**
     * Confirma a exclusão de uma notícia
     * @param {number} id - ID da notícia
     * @param {string} titulo - Título da notícia
     */
    window.confirmarExclusao = function(id, titulo) {
        const mensagem = `Tem certeza que deseja excluir a notícia "${titulo}"?\n\nEsta ação não pode ser desfeita.`;
        
        if (confirm(mensagem)) {
            // Adicionar loading ao botão
            const deleteBtn = document.querySelector(`a[href="deletarNoticia.php?id=${id}"]`);
            if (deleteBtn) {
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';
                deleteBtn.style.pointerEvents = 'none';
            }
            
            // Redirecionar para exclusão
            window.location.href = `deletarNoticia.php?id=${id}`;
        }
    }

    // ====== EVENTOS DE TECLADO ======
    
    // Fechar modal com ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'flex') {
            fecharModalNoticia();
        }
    });

    // Navegação por teclado nos cards
    newsCards.forEach((card, index) => {
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        card.setAttribute('aria-label', `Ver notícia: ${card.querySelector('.news-title')?.textContent || 'Sem título'}`);
        
        card.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                card.click();
            }
        });
    });

    // ====== FECHAR MODAL AO CLICAR FORA ======
    
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            fecharModalNoticia();
        }
    });

    // ====== ANIMAÇÕES E EFEITOS ======
    
    // Adicionar efeitos hover aos cards
    newsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
        });
    });

    // Efeitos nos botões de ação
    editButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.stopPropagation();
            
            const card = this.closest('.news-card');
            const titulo = card.querySelector('.news-title')?.textContent || 'Sem título';
            const id = this.href.split('id=')[1];
            
            confirmarExclusao(id, titulo);
        });
        
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // ====== BOTÃO ADICIONAR NOTÍCIA ======
    
    if (addButton) {
        addButton.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(52, 152, 219, 0.4)';
        });
        
        addButton.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(52, 152, 219, 0.3)';
        });
    }

    // ====== FUNÇÕES AUXILIARES ======
    
    /**
     * Escapa HTML para prevenir XSS
     * @param {string} text - Texto a ser escapado
     * @returns {string} Texto escapado
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ====== CARREGAMENTO LAZY DE IMAGENS ======
    
    const images = document.querySelectorAll('img[src*="uploads/"]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.3s ease';
                
                img.onload = function() {
                    img.style.opacity = '1';
                };
                
                img.onerror = function() {
                    img.style.display = 'none';
                };
                
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));

    // ====== FEEDBACK VISUAL ======
    
    // Adicionar loading state aos botões
    const actionButtons = document.querySelectorAll('a[href*="alterarNoticia.php"], a[href*="deletarNoticia.php"]');
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('news-delete-btn')) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Carregando...';
                this.style.pointerEvents = 'none';
            }
        });
    });

    // ====== ESTADOS VAZIOS ======
    
    const emptyState = document.querySelector('.empty-state');
    if (emptyState) {
        emptyState.style.animation = 'fadeInUp 0.6s ease-out';
    }

    // ====== RESPONSIVIDADE ======
    
    // Ajustar layout em telas pequenas
    function adjustLayout() {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            // Ajustar tamanho dos cards em mobile
            newsCards.forEach(card => {
                card.style.margin = '0.5rem 0';
            });
        }
    }

    // Executar ajuste inicial
    adjustLayout();
    
    // Executar ajuste quando a janela for redimensionada
    window.addEventListener('resize', adjustLayout);

    // ====== ESTILOS DINÂMICOS ======
    
    // Adicionar estilos específicos para o modal
    const style = document.createElement('style');
    style.textContent = `
        .modal-noticia {
            transition: opacity 0.3s ease;
        }
        
        .modal-noticia-content {
            transition: transform 0.3s ease;
            transform: scale(0.9);
        }
        
        .news-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .news-edit-btn,
        .news-delete-btn {
            transition: transform 0.2s ease;
        }
        
        .news-edit-btn:hover,
        .news-delete-btn:hover {
            transform: scale(1.1);
        }
        
        .portal-add-btn {
            transition: all 0.3s ease;
        }
        
        .portal-add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        }
        
        .empty-state {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-noticia-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }
        
        .modal-edit-btn,
        .modal-delete-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .modal-edit-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .modal-edit-btn:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
        }
        
        .modal-delete-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        
        .modal-delete-btn:hover {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .modal-noticia-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .modal-edit-btn,
            .modal-delete-btn {
                width: 100%;
                justify-content: center;
            }
        }
    `;
    document.head.appendChild(style);

    // ====== INICIALIZAÇÃO ======
    
    console.log('Portal JS carregado com sucesso!');
});
const btn = document.getElementById('toggle-theme');
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
