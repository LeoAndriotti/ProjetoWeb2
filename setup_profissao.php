<?php
include_once './config/config.php';

try {
    // Criar tabela profissao
    $sql_criar_tabela = "CREATE TABLE IF NOT EXISTS profissao (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL
    )";
    $banco->exec($sql_criar_tabela);
    echo "✅ Tabela 'profissao' criada com sucesso!<br>";

    // Verificar se já existem profissões
    $stmt = $banco->query("SELECT COUNT(*) FROM profissao");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Inserir profissões padrão
        $profissoes = [
            'Jornalista',
            'Anunciante', 
            'Editor',
            'Repórter',
            'Fotógrafo',
            'Designer',
            'Administrador'
        ];

        $sql_inserir = "INSERT INTO profissao (nome) VALUES (?)";
        $stmt = $banco->prepare($sql_inserir);

        foreach ($profissoes as $profissao) {
            $stmt->execute([$profissao]);
        }
        echo "✅ Profissões padrão inseridas com sucesso!<br>";
    } else {
        echo "ℹ️ Profissões já existem no banco.<br>";
    }

    // Verificar se a coluna profissao existe na tabela usuarios
    $stmt = $banco->query("SHOW COLUMNS FROM usuarios LIKE 'profissao'");
    $coluna_existe = $stmt->fetch();

    if (!$coluna_existe) {
        // Adicionar coluna profissao
        $sql_adicionar_coluna = "ALTER TABLE usuarios ADD COLUMN profissao INT";
        $banco->exec($sql_adicionar_coluna);
        echo "✅ Coluna 'profissao' adicionada à tabela 'usuarios'!<br>";

        // Adicionar chave estrangeira
        try {
            $sql_fk = "ALTER TABLE usuarios ADD CONSTRAINT fk_usuario_profissao 
                      FOREIGN KEY (profissao) REFERENCES profissao(id)";
            $banco->exec($sql_fk);
            echo "✅ Chave estrangeira criada com sucesso!<br>";
        } catch (PDOException $e) {
            echo "ℹ️ Chave estrangeira já existe ou não foi possível criar.<br>";
        }
    } else {
        echo "ℹ️ Coluna 'profissao' já existe na tabela 'usuarios'.<br>";
    }

    echo "<br>🎉 Configuração concluída com sucesso!<br>";
    echo "<a href='portal.php'>Clique aqui para voltar ao portal</a>";

} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?> 