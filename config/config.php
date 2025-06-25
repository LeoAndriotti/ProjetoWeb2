<?php 
include_once 'classes/BancoDeDados.php';
$banco_de_dados = new BancoDeDados();
$banco = $banco_de_dados->obterConexao();
?>