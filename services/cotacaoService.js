export async function buscarCotacoes() {
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