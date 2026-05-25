<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $contenido = trim($_POST['contenido']);
  $imagen = $_FILES['imagen'];
  $nombre_imagen = null;

  if (!empty($imagen['name'])) {
    $nombre = basename($imagen['name']);
    $nombre_unico = uniqid() . '_' . $nombre;
    $ruta_destino = 'uploads/' . $nombre_unico;
    $nombre_imagen = $nombre_unico; // ✅ Guardamos solo el nombre, no la ruta

    // Crear la carpeta si no existe
    if (!is_dir('uploads')) {
      mkdir('uploads', 0755, true);
    }

    // Mover el archivo subido
    move_uploaded_file($imagen['tmp_name'], $ruta_destino);
  }

  // Insertar en la base de datos
  $stmt = $conn->prepare("INSERT INTO publicaciones (usuario_id, contenido, imagen, creado_en) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("iss", $usuario_id, $contenido, $nombre_imagen);
  $stmt->execute();

  header("Location: index.php");
  exit;
}
?>

