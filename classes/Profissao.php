<?php
class Profissao {
    private $conexao;
    private $nome_tabela = "profissao";

    public function __construct($banco) {
        $this->conexao = $banco;
    }

    public function lerPorId($id) {
        $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE id = ?";
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function lerTodas() {
        $consulta = "SELECT * FROM " . $this->nome_tabela;
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 