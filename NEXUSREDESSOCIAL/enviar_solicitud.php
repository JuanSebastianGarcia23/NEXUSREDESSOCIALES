<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$de = $_SESSION['usuario_id'];
$para = isset($_POST['para_usuario_id']) ? intval($_POST['para_usuario_id']) : 0;

if ($de === $para || $para === 0) {
  // No puedes enviarte una solicitud a ti mismo o a un usuario inválido
  header("Location: perfil_publico.php?id=$para");
  exit;
}

// Verificar si ya hay una solicitud o amistad
$existe = $conn->query("SELECT * FROM amistades WHERE 
    (solicitante_id = $de AND receptor_id = $para) 
 OR (solicitante_id = $para AND receptor_id = $de)");

if ($existe->num_rows === 0) {
  $conn->query("INSERT INTO amistades (solicitante_id, receptor_id, estado) VALUES ($de, $para, 'pendiente')");
}

header("Location: perfil_publico.php?id=$para");
exit;
?>
