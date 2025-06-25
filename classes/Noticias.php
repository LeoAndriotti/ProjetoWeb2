<?php
class Noticias{
    private $conexao;
    private $nome_tabela = "noticias";   
    public $id;
    public $titulo;
    public $conteudo;
    public $data;
    public $id_autor;
    public $id_categoria;
    public $imagem;
    
    public function __construct($banco){
        $this -> conexao = $banco;
    }   
        public function registrar($titulo, $noticia, $data, $autor, $categoria, $imagem){
            $consulta = "INSERT INTO " . $this->nome_tabela . " (titulo, noticia, data, autor, categoria, imagem) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$titulo, $noticia, $data, $autor, $categoria, $imagem]);
            return $stmt;
        }

        public function criar($titulo, $noticia, $data, $autor, $id_categoria, $imagem){
            return $this->registrar($titulo, $noticia, $data, $autor, $id_categoria, $imagem);
        }
        
        public function ler() {
            $consulta = "SELECT * FROM " . $this->nome_tabela . " ORDER BY data DESC, id DESC";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public function lerNoticia() {
            $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE id = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$this->id]);
            $linha = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($linha) {
                $this->titulo = $linha['titulo'];
                $this->conteudo = $linha['noticia'];
                $this->data = $linha['data'];
                $this->id_autor = $linha['autor'];
                $this->id_categoria = $linha['categoria'];
                $this->imagem = $linha['imagem'];
                return true;
            }
            return false;
        }
        
        public function lerPorId($id){
            $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE id = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        public function deletar($id){
            $consulta = "DELETE FROM " . $this->nome_tabela. " WHERE id = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$id]);
            return $stmt;
        }

        public function lerPorAutor($id_autor){
            $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE autor = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$id_autor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function lerPorCategoria($id_categoria){
            $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE categoria = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$id_categoria]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function atualizar($id, $titulo, $noticia, $categoria, $imagem){
            $consulta = "UPDATE " . $this->nome_tabela . " SET titulo = ?, noticia = ?, categoria = ?, imagem = ? WHERE id = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$titulo, $noticia, $categoria, $imagem, $id]);
            return $stmt;
        }
}
?>