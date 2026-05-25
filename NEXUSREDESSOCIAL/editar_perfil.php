<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nuevo_usuario = $conn->real_escape_string($_POST['usuario']);
  $nuevo_email = $conn->real_escape_string($_POST['email']);

  // Verificar si hay una nueva imagen
  if (!empty($_FILES['foto']['name'])) {
    $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['foto']['type'], $permitidos)) {
      $nombre_archivo = basename($_FILES['foto']['name']);
      $ruta_destino = 'uploads/' . $nombre_archivo;

      // Verifica que la carpeta exista
      if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
      }

      if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
        // Actualiza incluyendo foto
        $conn->query("UPDATE usuarios SET usuario = '$nuevo_usuario', email = '$nuevo_email', foto = '$nombre_archivo' WHERE id = $usuario_id");
      }
    }
  } else {
    // Actualiza sin foto
    $conn->query("UPDATE usuarios SET usuario = '$nuevo_usuario', email = '$nuevo_email' WHERE id = $usuario_id");
  }

  header("Location: perfil.php");
  exit;
}

// Obtener usuario
$res = $conn->query("SELECT * FROM usuarios WHERE id = $usuario_id");
$usuario = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Perfil | NEXUS</title>
  <link rel="stylesheet" href="css/perfil.css">
</head>
<body>

  <div class="container">
    <h2>Editar Perfil</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Usuario:</label>
      <input type="text" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

      <label>Foto de perfil:</label>
      <input type="file" name="foto">

      <button type="submit">Guardar cambios</button>
    </form>
  </div>

</body>
</html>

