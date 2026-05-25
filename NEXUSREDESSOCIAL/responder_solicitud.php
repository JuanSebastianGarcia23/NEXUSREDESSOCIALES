<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];
$amistad_id = intval($_POST['amistad_id']);
$accion = $_POST['accion'];

if ($accion === 'aceptar') {
  $conn->query("UPDATE amistades SET estado = 'aceptada' WHERE id = $amistad_id AND receptor_id = $usuario_id");
} elseif ($accion === 'rechazar') {
  $conn->query("DELETE FROM amistades WHERE id = $amistad_id AND receptor_id = $usuario_id");
}

header("Location: solicitudes.php");
exit;
?>
