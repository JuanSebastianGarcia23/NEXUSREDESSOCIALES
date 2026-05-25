<?php
include 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usuario = $_POST['usuario'];
  $contrasena = $_POST['contrasena'];

  $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' OR email = '$usuario'";
  $result = $conn->query($sql);

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if ($contrasena === $row['contrasena']) {
      $_SESSION['usuario_id'] = $row['id'];
      header('Location: index.php'); // 👉 ahora redirige a index.php
      exit;
    }
  }

  $error = "Credenciales incorrectas";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión - NEXUS</title>
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
  <div class="form-container">
    <h2>Inicia sesión en NEXUS</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="login.php">
      <input type="text" name="usuario" placeholder="Usuario o correo" required>
      <input type="password" name="contrasena" placeholder="Contraseña" required>
      <button type="submit">Entrar</button>
    </form>
    <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
  </div>
</body>
</html>
