// Script para atualização de moedas em tempo real
class MoedasUpdater {
    constructor() {
        this.updateInterval = 30000; // Atualiza a cada 30 segundos
        this.isUpdating = false;
        this.init();
    }

    init() {
        // Inicia a primeira atualização
        this.updateMoedas();
        
        // Configura atualização automática
        setInterval(() => {
            this.updateMoedas();
        }, this.updateInterval);
    }

    async updateMoedas() {
        if (this.isUpdating) return;
        
        this.isUpdating = true;
        
        try {
            // Busca cotações do Banco Central do Brasil
            const response = await fetch('https://economia.awesomeapi.com.br/last/USD-BRL,EUR-BRL,BTC-BRL,GBP-BRL');
            
            if (!response.ok) {
                throw new Error('Erro ao buscar cotações');
            }
            
            const data = await response.json();
            
            // Atualiza os valores na interface
            this.updateCurrencyValue('usd-value', data.USDBRL?.bid || '5,56');
            this.updateCurrencyValue('eur-value', data.EURBRL?.bid || '6,48');
            this.updateCurrencyValue('btc-value', data.BTCBRL?.bid || '597.466,26');
            this.updateCurrencyValue('gbp-value', data.GBPBRL?.bid || '7,59');
            
            console.log('Moedas atualizadas com sucesso:', new Date().toLocaleTimeString());
            
        } catch (error) {
            console.error('Erro ao atualizar moedas:', error);
            // Em caso de erro, mantém os valores padrão
        } finally {
            this.isUpdating = false;
        }
    }

    updateCurrencyValue(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            // Formata o valor para exibição
            const formattedValue = this.formatCurrencyValue(value);
            element.textContent = formattedValue;
            
            // Adiciona efeito visual de atualização
            element.style.transition = 'color 0.3s ease';
            element.style.color = '#28a745';
            
            setTimeout(() => {
                element.style.color = '';
            }, 1000);
        }
    }

    formatCurrencyValue(value) {
        if (!value) return '0,00';
        
        // Converte para número
        const numValue = parseFloat(value);
        
        if (isNaN(numValue)) return '0,00';
        
        // Formata baseado no valor
        if (numValue >= 1000) {
            // Para valores grandes como BTC
            return numValue.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        } else {
            // Para valores menores como moedas
            return numValue.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    // Método para forçar atualização manual
    forceUpdate() {
        this.updateMoedas();
    }

    // Método para alterar intervalo de atualização
    setUpdateInterval(seconds) {
        this.updateInterval = seconds * 1000;
        // Reinicia o intervalo
        clearInterval(this.updateInterval);
        setInterval(() => {
            this.updateMoedas();
        }, this.updateInterval);
    }
}

// Inicializa o atualizador quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    window.moedasUpdater = new MoedasUpdater();
});

// Função global para forçar atualização (pode ser chamada de outros scripts)
function atualizarMoedas() {
    if (window.moedasUpdater) {
        window.moedasUpdater.forceUpdate();
    }
} 