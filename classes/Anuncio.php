<?php
class Anuncio {
    private $conexao;
    private $nome_tabela = "anuncios";
    public $id;
    public $nome;
    public $imagem;
    public $link;
    public $ativo;
    public $destaque;
    public $dataCadastro;
    public $valorAnuncio;
    public $texto;

    public function __construct($banco) {
        $this->conexao = $banco;
    }

    public function registrar($nome, $imagem, $link, $ativo, $destaque, $dataCadastro, $valorAnuncio, $texto) {
        $consulta = "INSERT INTO " . $this->nome_tabela . " (nome, imagem, link, ativo, destaque, dataCadastro, valorAnuncio, texto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute([$nome, $imagem, $link, $ativo, $destaque, $dataCadastro, $valorAnuncio, $texto]);
        return $stmt;
    }

    public function criar($nome, $imagem, $link, $ativo, $destaque, $dataCadastro, $valorAnuncio, $texto) {
        return $this->registrar($nome, $imagem, $link, $ativo, $destaque, $dataCadastro, $valorAnuncio, $texto);
    }

    public function ler() {
        $consulta = "SELECT * FROM " . $this->nome_tabela . " ORDER BY dataCadastro DESC, id DESC";
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lerPorId($id) {
        $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE id = ?";
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deletar($id) {
        $consulta = "DELETE FROM " . $this->nome_tabela . " WHERE id = ?";
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute([$id]);
        return $stmt;
    }

    public function atualizar($id, $nome, $imagem, $link, $ativo, $destaque, $dataCadastro, $valorAnuncio, $texto) {
        $consulta = "UPDATE " . $this->nome_tabela . " SET nome = ?, imagem = ?, link = ?, ativo = ?, destaque = ?, dataCadastro = ?, valorAnuncio = ?, texto = ? WHERE id = ?";
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute([$nome, $imagem, $link, $ativo, $destaque, $dataCadastro, $valorAnuncio, $texto, $id]);
        return $stmt;
    }

    public function lerAtivos() {
        $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE ativo = 1 ORDER BY dataCadastro DESC, id DESC";
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lerDestaques() {
        $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE destaque = 1 ORDER BY dataCadastro DESC, id DESC";
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lerPorNomeAnunciante($nome) {
        $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE nome = ? ORDER BY dataCadastro DESC, id DESC";
        $stmt = $this->conexao->prepare($consulta);
        $stmt->execute([$nome]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
