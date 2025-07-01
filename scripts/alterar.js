document.addEventListener('DOMContentLoaded', function() {
    
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

    // Obtém referências aos elementos do formulário
    const form = document.querySelector('.portal-form');
    const nomeInput = document.getElementById('nome');
    const emailInput = document.getElementById('email');
    const sexoInputs = document.querySelectorAll('input[name="sexo"]');

    // Adiciona listener para validação no envio do formulário
    form.addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessage = '';

        if (nomeInput.value.trim().length < 2) {
            isValid = false;
            errorMessage += 'Nome deve ter pelo menos 2 caracteres.\n';
            nomeInput.style.borderColor = '#ff4444';
        } else {
            nomeInput.style.borderColor = '';
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            isValid = false;
            errorMessage += 'Email inválido.\n';
            emailInput.style.borderColor = '#ff4444';
        } else {
            emailInput.style.borderColor = '';
        }

        const foneValue = foneInput.value.replace(/\D/g, '');
        if (foneValue.length < 10 || foneValue.length > 11) {
            isValid = false;
            errorMessage += 'Telefone deve ter 10 ou 11 dígitos.\n';
            foneInput.style.borderColor = '#ff4444';
        } else {
            foneInput.style.borderColor = '';
        }

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

    // Confirmação antes de salvar as alterações
    form.addEventListener('submit', function(e) {
        if (!confirm('Tem certeza que deseja salvar as alterações?')) {
            e.preventDefault();
        }
    });

    // Remove formatação do telefone antes de enviar o formulário
    form.addEventListener('submit', function() {
        const foneValue = foneInput.value.replace(/\D/g, '');
        foneInput.value = foneValue;
    });

    // Previne envio do formulário quando Enter é pressionado em campos de texto
    const textInputs = document.querySelectorAll('input[type="text"], input[type="email"]');
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

    // Atalho ESC para sair da página com confirmação
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (confirm('Deseja sair sem salvar as alterações?')) {
                window.location.href = 'portal.php';
            }
        }
    });

    // Botão de voltar com confirmação se houver alterações
    const voltarBtn = document.querySelector('a[href="portal.php"]');
    if (voltarBtn) {
        voltarBtn.addEventListener('click', function(e) {
            if (formHasChanges()) {
                if (!confirm('Deseja sair sem salvar as alterações?')) {
                    e.preventDefault();
                }
            }
        });
    }

    function formHasChanges() {
        const originalValues = {
            nome: nomeInput.defaultValue,
            email: emailInput.defaultValue,
            fone: foneInput.defaultValue,
            sexo: document.querySelector('input[name="sexo"]:checked')?.value
        };

        const currentValues = {
            nome: nomeInput.value,
            email: emailInput.value,
            fone: foneInput.value,
            sexo: document.querySelector('input[name="sexo"]:checked')?.value
        };

        return JSON.stringify(originalValues) !== JSON.stringify(currentValues);
    }

    // Salva os valores originais para comparação posterior
    nomeInput.defaultValue = nomeInput.value;
    emailInput.defaultValue = emailInput.value;
    foneInput.defaultValue = foneInput.value;
});