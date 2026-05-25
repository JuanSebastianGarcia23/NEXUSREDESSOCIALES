<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$mi_id = $_SESSION['usuario_id'];

// Buscar amigos (la amistad puede estar en ambos sentidos)
$amigos = $conn->query("
  SELECT u.id, u.usuario FROM amistades a 
  JOIN usuarios u ON 
    (u.id = a.usuario_id_1 AND a.usuario_id_2 = $mi_id) OR 
    (u.id = a.usuario_id_2 AND a.usuario_id_1 = $mi_id)
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Amigos - NEXUS</title>
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
  <header class="navbar">
    <div class="logo">NEXUS</div>
    <a href="index.php">🏠 Inicio</a>
    <a href="solicitudes.php">🔔 Solicitudes</a>
    <a href="logout.php">Cerrar sesión</a>
  </header>

  <div class="container">
    <main class="main-feed">
      <h2>Mis amigos</h2>
      <?php if ($amigos->num_rows > 0): ?>
        <ul>
          <?php while ($amigo = $amigos->fetch_assoc()): ?>
            <li>
              <a href="perfil_publico.php?id=<?= $amigo['id'] ?>">
                <?= htmlspecialchars($amigo['usuario']) ?>
              </a>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p>Aún no tienes amigos.</p>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
