<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
 header('Location: index.php');
 exit();
}
include_once './config/config.php';
include_once './classes/Anuncio.php';
$anuncio = new Anuncio($banco);
if (isset($_GET['id'])) {
 $id = $_GET['id'];
 $anuncio->deletar($id);
 header('Location: portalAnunciante.php');
 exit();
}
?>