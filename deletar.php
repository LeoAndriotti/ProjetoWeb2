<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
 header('Location: index.php');
 exit();
}
include_once './config/config.php';
include_once './classes/Usuario.php';
$usuario = new Usuario($banco);
if (isset($_GET['id'])) {
 $id = $_GET['id'];
 $usuario->deletar($id);
 header('Location: portal.php');
 exit();
}
?>