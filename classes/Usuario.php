<?php
class Usuario{
    private $conexao;
    private $nome_tabela = "usuarios";   
    
    public function __construct($banco){
        $this -> conexao = $banco;
    }   
        public function registrar($nome, $sexo, $fone, $email, $senha){
            $consulta = "INSERT INTO " . $this->nome_tabela . " (nome, sexo, fone, email, senha) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conexao->prepare($consulta);
            $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
            $stmt->execute([$nome, $sexo, $fone, $email, $senha_hash]);
            return $stmt;
        }

        public function fazerLogin($email, $senha){
            $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE email = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if($usuario && password_verify($senha, $usuario['senha'])){
                return $usuario;
            } 
            return false;
        }

        public function criar($nome, $sexo, $fone, $email, $senha){
            return $this->registrar($nome, $sexo, $fone, $email, $senha);
        }
        
        public function ler(){
            $consulta = "SELECT * FROM " . $this->nome_tabela;
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function lerPorId($id){
            $consulta = "SELECT * FROM " . $this->nome_tabela . " WHERE id = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        public function atualizar($id, $nome, $sexo, $fone, $email){
            $consulta = "UPDATE " . $this->nome_tabela . " SET nome = ?, sexo = ?, fone = ?, email = ? WHERE id = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$nome, $sexo, $fone, $email, $id]);
            return $stmt; 
        }
        public function deletar($id){
            $consulta = "DELETE FROM " . $this->nome_tabela. " WHERE id = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$id]);
            return $stmt;
        }

        public function buscarPorEmail($email) {
            $consulta = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = $this->conexao->prepare($consulta);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
}
?>