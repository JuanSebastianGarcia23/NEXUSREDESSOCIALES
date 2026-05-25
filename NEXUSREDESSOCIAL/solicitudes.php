<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];

// Solicitudes recibidas
$recibidas = $conn->query("
  SELECT a.id, u.usuario, u.foto_perfil 
  FROM amistades a 
  JOIN usuarios u ON a.solicitante_id = u.id 
  WHERE a.receptor_id = $usuario_id AND a.estado = 'pendiente'
");

// Solicitudes enviadas
$enviadas = $conn->query("
  SELECT a.id, u.usuario, u.foto_perfil 
  FROM amistades a 
  JOIN usuarios u ON a.receptor_id = u.id 
  WHERE a.solicitante_id = $usuario_id AND a.estado = 'pendiente'
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitudes de amistad | NEXUS</title>
  <link rel="stylesheet" href="css/perfil.css">
</head>
<body>

  <header class="navbar">
    <div class="logo">NEXUS</div>
    <a href="index.php">🏠 Inicio</a>
    <a href="perfil.php">👤 Perfil</a>
    <a href="solicitudes.php">👥 Solicitudes</a>
    <div class="user-info">
      <a href="logout.php">Cerrar sesión</a>
    </div>
  </header>

  <div class="container">
    <h2>Solicitudes recibidas</h2>
    <?php while ($row = $recibidas->fetch_assoc()): ?>
      <div class="solicitud">
        <img src="uploads/<?= htmlspecialchars($row['foto_perfil'] ?? 'default.jpg') ?>" width="50">
        <strong><?= htmlspecialchars($row['usuario']) ?></strong>
        <form action="responder_solicitud.php" method="POST" style="display:inline;">
          <input type="hidden" name="amistad_id" value="<?= $row['id'] ?>">
          <button name="accion" value="aceptar">Aceptar</button>
          <button name="accion" value="rechazar">Rechazar</button>
        </form>
      </div>
    <?php endwhile; ?>

    <h2>Solicitudes enviadas</h2>
    <?php while ($row = $enviadas->fetch_assoc()): ?>
      <div class="solicitud">
        <img src="uploads/<?= htmlspecialchars($row['foto_perfil'] ?? 'default.jpg') ?>" width="50">
        <strong><?= htmlspecialchars($row['usuario']) ?></strong>
        <span>Solicitud enviada</span>
      </div>
    <?php endwhile; ?>
  </div>

</body>
</html>
