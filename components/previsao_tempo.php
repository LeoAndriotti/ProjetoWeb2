<?php
// Componente de Previsão do Tempo usando API OpenWeather
// Chave da API: 27acfa0d5be8d879ce61f4b0249eb68b

// Verifica se é uma requisição AJAX para buscar previsão por coordenadas
if (isset($_GET['action']) && $_GET['action'] === 'get_weather' && isset($_GET['lat']) && isset($_GET['lon'])) {
    header('Content-Type: application/json');
    
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
    $api_key = '27acfa0d5be8d879ce61f4b0249eb68b';
    
    // Busca previsão atual por coordenadas
    $url_atual = "http://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$api_key}&units=metric&lang=pt_br";
    
    // Busca previsão de 5 dias por coordenadas
    $url_5dias = "http://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$api_key}&units=metric&lang=pt_br";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 15,
            'user_agent' => 'CSL Times Weather Widget'
        ]
    ]);
    
    // Busca dados atuais
    $response_atual = file_get_contents($url_atual, false, $context);
    
    // Se file_get_contents falhar, tenta com cURL
    if ($response_atual === false) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_atual);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'CSL Times Weather Widget');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response_atual = curl_exec($ch);
            curl_close($ch);
            
            if ($response_atual === false) {
                echo json_encode(['success' => false, 'message' => 'Erro ao acessar API via cURL']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao acessar API']);
            exit;
        }
    }
    
    // Busca dados de 5 dias
    $response_5dias = file_get_contents($url_5dias, false, $context);
    
    if ($response_5dias === false) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_5dias);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'CSL Times Weather Widget');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response_5dias = curl_exec($ch);
            curl_close($ch);
        }
    }
    
    $dados_atual = json_decode($response_atual, true);
    $dados_5dias = json_decode($response_5dias, true);
    
    if ($dados_atual === null || isset($dados_atual['cod']) && $dados_atual['cod'] !== 200) {
        echo json_encode(['success' => false, 'message' => 'Erro ao processar dados da API']);
        exit;
    }
    
    // Processa dados atuais
    $temperatura = round($dados_atual['main']['temp']);
    $icone = obterIconeTempo($dados_atual['weather'][0]['icon']);
    $cidade_nome = $dados_atual['name'];
    
    // Processa dados de 5 dias
    $previsao_5dias = [];
    if ($dados_5dias && isset($dados_5dias['list'])) {
        $dias_processados = [];
        
        foreach ($dados_5dias['list'] as $item) {
            $data = new DateTime($item['dt_txt']);
            $dia_semana = $data->format('w'); // 0 = domingo, 1 = segunda, etc.
            $hora = $data->format('H');
            
            // Pega apenas uma previsão por dia (meio-dia)
            if (!isset($dias_processados[$dia_semana]) && $hora >= 12) {
                $dias_processados[$dia_semana] = true;
                
                $nome_dia = [
                    '0' => 'Dom',
                    '1' => 'Seg',
                    '2' => 'Ter',
                    '3' => 'Qua',
                    '4' => 'Qui',
                    '5' => 'Sex',
                    '6' => 'Sáb'
                ];
                
                $previsao_5dias[] = [
                    'dia' => $nome_dia[$dia_semana],
                    'temperatura' => round($item['main']['temp']),
                    'icone' => obterIconeTempo($item['weather'][0]['icon'])
                ];
                
                // Limita a 5 dias
                if (count($previsao_5dias) >= 7) break;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'temperatura' => $temperatura,
        'icone' => $icone,
        'cidade' => $cidade_nome,
        'previsao_5dias' => $previsao_5dias
    ]);
    exit;
}

function obterPrevisaoTempo($cidade = 'São Paulo', $pais = 'BR') {
    $api_key = '27acfa0d5be8d879ce61f4b0249eb68b';
    // Codifica a cidade para evitar problemas com caracteres especiais
    $cidade_codificada = urlencode($cidade);
    $url = "http://api.openweathermap.org/data/2.5/weather?q={$cidade_codificada},{$pais}&appid={$api_key}&units=metric&lang=pt_br";
    
    // Tenta primeiro com file_get_contents
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'CSL Times Weather Widget'
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    // Se file_get_contents falhar, tenta com cURL
    if ($response === false) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'CSL Times Weather Widget');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($response === false || $http_code !== 200) {
                error_log("Erro ao acessar API OpenWeather via cURL: HTTP $http_code");
                return null;
            }
        } else {
            error_log("Erro ao acessar API OpenWeather: " . error_get_last()['message']);
            return null;
        }
    }
    
    $dados = json_decode($response, true);
    
    if ($dados === null) {
        error_log("Erro ao decodificar JSON da API OpenWeather");
        return null;
    }
    
    if (isset($dados['cod']) && $dados['cod'] !== 200) {
        error_log("API OpenWeather retornou erro: " . ($dados['message'] ?? 'Erro desconhecido'));
        return null;
    }
    
    return $dados;
}

function obterIconeTempo($codigo) {
    $icones = [
        '01d' => '☀️', // céu limpo
        '01n' => '🌙', // céu limpo (noite)
        '02d' => '⛅', // poucas nuvens
        '02n' => '☁️', // poucas nuvens (noite)
        '03d' => '☁️', // nuvens dispersas
        '03n' => '☁️', // nuvens dispersas (noite)
        '04d' => '☁️', // nuvens quebradas
        '04n' => '☁️', // nuvens quebradas (noite)
        '09d' => '🌧️', // chuva leve
        '09n' => '🌧️', // chuva leve (noite)
        '10d' => '🌦️', // chuva
        '10n' => '🌧️', // chuva (noite)
        '11d' => '⛈️', // trovão
        '11n' => '⛈️', // trovão (noite)
        '13d' => '🌨️', // neve
        '13n' => '🌨️', // neve (noite)
        '50d' => '🌫️', // névoa
        '50n' => '🌫️', // névoa (noite)
    ];
    
    return $icones[$codigo] ?? '🌤️';
}

function renderizarPrevisaoTempo($cidade = 'São Paulo', $pais = 'BR') {
    $dados = obterPrevisaoTempo($cidade, $pais);
    
    if (!$dados) {
        return '<div class="previsao-tempo-simples erro">
            <span class="icone-tempo">🌤️</span>
            <span class="temperatura">--°C</span>
            <span class="cidade">Localização</span>
        </div>';
    }
    
    $temperatura = round($dados['main']['temp']);
    $icone = obterIconeTempo($dados['weather'][0]['icon']);
    $cidade_nome = $dados['name'];
    
    return "
    <div class='previsao-tempo-simples' data-cidade='{$cidade_nome}' data-temperatura='{$temperatura}' data-icone='{$icone}'>
        <span class='icone-tempo'>{$icone}</span>
        <span class='temperatura'>{$temperatura}°C</span>
        <span class='cidade'>{$cidade_nome}</span>
    </div>";
}
?>