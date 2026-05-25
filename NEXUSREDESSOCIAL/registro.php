<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usuario = $_POST['usuario'];
  $email = $_POST['email'];
  $contrasena = $_POST['contrasena']; // SIN hash

  $sql = "INSERT INTO usuarios (usuario, email, contrasena) VALUES ('$usuario', '$email', '$contrasena')";
  $conn->query($sql);
  header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - NEXUS</title>
  <link rel="stylesheet" href="css/registro.css">
</head>
<body>
  <div class="form-container">
    <h2>Crear cuenta en NEXUS</h2>
    <form method="POST">
      <input type="text" name="usuario" placeholder="Usuario" required>
      <input type="email" name="email" placeholder="Correo" required>
      <input type="password" name="contrasena" placeholder="Contraseña" required>
      <button type="submit">Registrar</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión</a></p>
  </div>
</body>
</html>
