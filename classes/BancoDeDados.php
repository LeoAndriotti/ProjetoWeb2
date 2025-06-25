<?php
class BancoDeDados {
    private $servidor = "localhost";
    private $nome_banco = "bdcrud";
    private $usuario = "root";
    private $senha = "";
    public $conexao;
    public function obterConexao() {
        $this->conexao = null;
        try {
            $this->conexao = new PDO("mysql:host=" . $this->servidor .
                ";dbname=" . $this->nome_banco, $this->usuario, $this->senha);

        } catch(PDOException $excecao) {
            echo "Erro de conexão: " . $excecao->getMessage();
        }
        return $this->conexao;
    }
}
?>