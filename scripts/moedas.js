// Script para atualização de moedas em tempo real

document.addEventListener('DOMContentLoaded', function() {
    async function buscarCotacoes() {
        const url = 'https://economia.awesomeapi.com.br/last/USD-BRL,EUR-BRL,BTC-BRL,GBP-BRL';
        const response = await fetch(url);
        if (!response.ok) throw new Error('Erro ao buscar cotações');
        const data = await response.json();
        return {
            USD: data.USDBRL?.bid,
            EUR: data.EURBRL?.bid,
            BTC: data.BTCBRL?.bid,
            GBP: data.GBPBRL?.bid
        };
    }

    async function atualizarMoedas() {
        try {
            const cotacoes = await buscarCotacoes();
            if (cotacoes.USD) document.getElementById('usd-value').textContent = parseFloat(cotacoes.USD).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
            if (cotacoes.EUR) document.getElementById('eur-value').textContent = parseFloat(cotacoes.EUR).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
            if (cotacoes.BTC) document.getElementById('btc-value').textContent = parseFloat(cotacoes.BTC).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
            if (cotacoes.GBP) document.getElementById('gbp-value').textContent = parseFloat(cotacoes.GBP).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
        } catch (e) {
            document.getElementById('usd-value').textContent = '--';
            document.getElementById('eur-value').textContent = '--';
            document.getElementById('btc-value').textContent = '--';
            document.getElementById('gbp-value').textContent = '--';
        }
    }
    atualizarMoedas();
    setInterval(atualizarMoedas, 60000); // Atualiza a cada 60 segundos
});

// Função global para forçar atualização (pode ser chamada de outros scripts)
function atualizarMoedas() {
    if (window.moedasUpdater) {
        window.moedasUpdater.forceUpdate();
    }
} 