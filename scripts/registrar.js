
document.addEventListener('DOMContentLoaded', function() {
    
    // ====== FORMATAÇÃO AUTOMÁTICA DE TELEFONE ======
    // Obtém referência ao campo de telefone
    const foneInput = document.getElementById('fone');
    
    // Adiciona listener para formatar o telefone enquanto o usuário digita
    foneInput.addEventListener('input', function(e) {
        let valor = e.target.value;
        
        // Remove todos os caracteres não numéricos
        valor = valor.replace(/\D/g, '');
        
        // Limita a 11 dígitos (DDD + 9 dígitos)
        valor = valor.slice(0, 11);

        // Aplica formatação baseada no número de dígitos
        if (valor.length > 6) {
            // Formato: (11) 99999-9999
            valor = valor.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        } else if (valor.length > 2) {
            // Formato: (11) 99999
            valor = valor.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else if (valor.length > 0) {
            // Formato: (11
            valor = valor.replace(/^(\d*)/, '($1');
        }

        // Atualiza o valor do campo
        e.target.value = valor;
    });

    // ====== VALIDAÇÃO DO FORMULÁRIO ======
    // Obtém referências aos elementos do formulário
    const form = document.querySelector('.portal-form');
    const nomeInput = document.getElementById('nome');
    const emailInput = document.getElementById('email');
    const senhaInput = document.getElementById('senha');
    const sexoInputs = document.querySelectorAll('input[name="sexo"]');

    // Adiciona listener para validação no envio do formulário
    form.addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessage = '';

        // ====== VALIDAÇÃO DO NOME ======
        if (nomeInput.value.trim().length < 2) {
            isValid = false;
            errorMessage += 'Nome deve ter pelo menos 2 caracteres.\n';
            nomeInput.style.borderColor = '#ff4444';
        } else {
            nomeInput.style.borderColor = '';
        }

        // ====== VALIDAÇÃO DO EMAIL ======
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            isValid = false;
            errorMessage += 'Email inválido.\n';
            emailInput.style.borderColor = '#ff4444';
        } else {
            emailInput.style.borderColor = '';
        }

        // ====== VALIDAÇÃO DO TELEFONE ======
        const foneValue = foneInput.value.replace(/\D/g, '');
        if (foneValue.length < 10 || foneValue.length > 11) {
            isValid = false;
            errorMessage += 'Telefone deve ter 10 ou 11 dígitos.\n';
            foneInput.style.borderColor = '#ff4444';
        } else {
            foneInput.style.borderColor = '';
        }

        // ====== VALIDAÇÃO DA SENHA ======
        const senha = senhaInput.value;
        if (senha.length < 6) {
            isValid = false;
            errorMessage += 'Senha deve ter pelo menos 6 caracteres.\n';
            senhaInput.style.borderColor = '#ff4444';
        } else {
            senhaInput.style.borderColor = '';
        }

        // ====== VALIDAÇÃO DO SEXO ======
        let sexoSelecionado = false;
        sexoInputs.forEach(input => {
            if (input.checked) {
                sexoSelecionado = true;
            }
        });
        if (!sexoSelecionado) {
            isValid = false;
            errorMessage += 'Selecione o sexo.\n';
            document.querySelector('.form-row').style.borderColor = '#ff4444';
        } else {
            document.querySelector('.form-row').style.borderColor = '';
        }

        // Se houver erros, impede o envio e exibe mensagem
        if (!isValid) {
            e.preventDefault();
            alert('Por favor, corrija os seguintes erros:\n' + errorMessage);
        } else {
            // Confirmação antes de enviar
            if (!confirm('Tem certeza que deseja cadastrar este usuário?')) {
                e.preventDefault();
            }
        }
    });

    
    // Validação do nome quando o campo perde o foco
    nomeInput.addEventListener('blur', function() {
        if (this.value.trim().length < 2) {
            this.style.borderColor = '#ff4444';
            this.title = 'Nome deve ter pelo menos 2 caracteres';
        } else {
            this.style.borderColor = '';
            this.title = '';
        }
    });

    // Validação do email quando o campo perde o foco
    emailInput.addEventListener('blur', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(this.value)) {
            this.style.borderColor = '#ff4444';
            this.title = 'Digite um email válido';
        } else {
            this.style.borderColor = '';
            this.title = '';
        }
    });

    // Validação do telefone quando o campo perde o foco
    foneInput.addEventListener('blur', function() {
        const foneValue = this.value.replace(/\D/g, '');
        if (foneValue.length < 10 || foneValue.length > 11) {
            this.style.borderColor = '#ff4444';
            this.title = 'Telefone deve ter 10 ou 11 dígitos';
        } else {
            this.style.borderColor = '';
            this.title = '';
        }
    });

    // Validação da senha em tempo real
    senhaInput.addEventListener('input', function() {
        const senha = this.value;
        const forcaSenha = calcularForcaSenha(senha);
        
        // Remove classes anteriores
        this.classList.remove('senha-fraca', 'senha-media', 'senha-forte');
        
        // Adiciona classe baseada na força da senha
        if (senha.length > 0) {
            if (forcaSenha < 3) {
                this.classList.add('senha-fraca');
                this.title = 'Senha fraca - Adicione letras maiúsculas, minúsculas e números';
            } else if (forcaSenha < 5) {
                this.classList.add('senha-media');
                this.title = 'Senha média - Adicione caracteres especiais para maior segurança';
            } else {
                this.classList.add('senha-forte');
                this.title = 'Senha forte!';
            }
        } else {
            this.title = '';
        }
    });

    function calcularForcaSenha(senha) {
        let forca = 0;
        
        if (senha.length >= 6) forca++;
        if (senha.length >= 8) forca++;
        if (/[a-z]/.test(senha)) forca++;
        if (/[A-Z]/.test(senha)) forca++;
        if (/\d/.test(senha)) forca++;
        if (/[^A-Za-z0-9]/.test(senha)) forca++;
        
        return forca;
    }

    
    // Previne envio do formulário quando Enter é pressionado em campos de texto
    const textInputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
    textInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                
                // Foca no próximo campo ou no botão de submit
                const nextInput = this.parentElement.nextElementSibling?.querySelector('input');
                if (nextInput) {
                    nextInput.focus();
                } else {
                    document.querySelector('.submit-btn').focus();
                }
            }
        });
    });

    // ====== FEEDBACK VISUAL ======
    
    // Função para adicionar feedback visual positivo aos campos
    function addValidFeedback(input) {
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.style.borderColor = '#28a745';
            } else {
                this.style.borderColor = '';
            }
        });
    }

    // Aplica feedback visual aos campos principais
    addValidFeedback(nomeInput);
    addValidFeedback(emailInput);
    addValidFeedback(foneInput);

    // ====== ATALHOS DE TECLADO ======
    
    // Atalho ESC para sair da página com confirmação
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (confirm('Deseja sair sem cadastrar o usuário?')) {
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
                if (!confirm('Deseja sair sem cadastrar o usuário?')) {
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
        return nomeInput.value.trim() !== '' ||
               emailInput.value.trim() !== '' ||
               foneInput.value.trim() !== '' ||
               senhaInput.value.trim() !== '' ||
               document.querySelector('input[name="sexo"]:checked') !== null;
    }

    // ====== LIMPEZA DE FORMATAÇÃO ======
    
    // Remove formatação do telefone antes de enviar o formulário
    form.addEventListener('submit', function() {
        const foneValue = foneInput.value.replace(/\D/g, '');
        foneInput.value = foneValue;
    });

    // ====== MENSAGENS DE SUCESSO/ERRO ======
    
    // Auto-oculta mensagens de sucesso após 5 segundos
    const successMessage = document.querySelector('.success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 500);
        }, 5000);
    }

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
});
