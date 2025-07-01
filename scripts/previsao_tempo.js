document.addEventListener('DOMContentLoaded', function() {
    const weatherWidget = document.querySelector('.previsao-tempo-simples');
    
    if (!weatherWidget) return;
    
    // Função para buscar previsão do tempo por coordenadas
    async function buscarPrevisaoPorCoordenadas(lat, lon) {
        try {
            console.log('Buscando previsão para:', lat, lon);
            
            // Mostra estado de carregamento
            weatherWidget.classList.add('carregando');
            weatherWidget.innerHTML = `
                <span class="icone-tempo">🔄</span>
                <span class="temperatura">--°C</span>
                <span class="cidade">Carregando...</span>
            `;
            
            const response = await fetch(`components/previsao_tempo.php?lat=${lat}&lon=${lon}&action=get_weather`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('Resposta da API:', data);
            
            if (data.success) {
                // Cria o HTML para a previsão atual (layout simples)
                let html = `
                    <span class="icone-tempo">${data.icone}</span>
                    <span class="temperatura">${data.temperatura}°C</span>
                    <span class="cidade">${data.cidade}</span>
                `;
                
                // Adiciona previsão de 5 dias se disponível (hidden por padrão)
                if (data.previsao_5dias && data.previsao_5dias.length > 0) {
                    html += '<div class="previsao-5dias">';
                    data.previsao_5dias.forEach(dia => {
                        html += `
                            <div class="dia-previsao">
                                <div class="dia">${dia.dia}</div>
                                <div class="icone">${dia.icone}</div>
                                <div class="temp">${dia.temperatura}°</div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }
                
                weatherWidget.innerHTML = html;
                weatherWidget.classList.remove('carregando', 'erro');
            } else {
                throw new Error(data.message || 'Erro ao buscar previsão');
            }
        } catch (error) {
            console.error('Erro ao buscar previsão:', error);
            weatherWidget.classList.remove('carregando');
            weatherWidget.classList.add('erro');
            weatherWidget.innerHTML = `
                <span class="icone-tempo">⚠️</span>
                <span class="temperatura">--°C</span>
                <span class="cidade">Erro</span>
            `;
        }
    }
    
    // Função para obter localização do usuário
    function obterLocalizacao() {
        if (navigator.geolocation) {
            console.log('Solicitando permissão de localização...');
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    console.log('Localização obtida:', position.coords);
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    buscarPrevisaoPorCoordenadas(lat, lon);
                },
                function(error) {
                    console.error('Erro ao obter localização:', error);
                    weatherWidget.classList.remove('carregando');
                    weatherWidget.classList.add('erro');
                    weatherWidget.innerHTML = `
                        <span class="icone-tempo">📍</span>
                        <span class="temperatura">--°C</span>
                        <span class="cidade">Permitir localização</span>
                    `;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 300000 // 5 minutos
                }
            );
        } else {
            console.log('Geolocalização não suportada');
            weatherWidget.classList.remove('carregando');
            weatherWidget.classList.add('erro');
            weatherWidget.innerHTML = `
                <span class="icone-tempo">❌</span>
                <span class="temperatura">--°C</span>
                <span class="cidade">Não suportado</span>
            `;
        }
    }
    
    // Inicia a busca de localização após um pequeno delay
    setTimeout(obterLocalizacao, 500);
    
    // Permite clicar no widget para atualizar
    weatherWidget.addEventListener('click', function() {
        console.log('Widget clicado - atualizando...');
        weatherWidget.classList.remove('erro');
        obterLocalizacao();
    });
}); 