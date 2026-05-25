<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$id_usuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener info del usuario visitado
$res = $conn->query("SELECT * FROM usuarios WHERE id = $id_usuario");
if ($res->num_rows !== 1) {
  echo "Usuario no encontrado";
  exit;
}
$perfil = $res->fetch_assoc();

// Obtener publicaciones del usuario
$posts = $conn->query("SELECT * FROM publicaciones WHERE usuario_id = $id_usuario ORDER BY creado_en DESC");

// Usuario logueado
$usuario_logueado = $_SESSION['usuario_id'];
$resLog = $conn->query("SELECT usuario FROM usuarios WHERE id = $usuario_logueado");
$usuario_nombre = ($resLog->num_rows === 1) ? $resLog->fetch_assoc()['usuario'] : 'Usuario';

// Obtener estado de amistad
$estado_amistad = null;
$stmt = $conn->prepare("SELECT estado FROM amistades 
  WHERE (solicitante_id = ? AND receptor_id = ?) 
     OR (solicitante_id = ? AND receptor_id = ?)");
$stmt->bind_param("iiii", $usuario_logueado, $id_usuario, $id_usuario, $usuario_logueado);
$stmt->execute();
$resAmistad = $stmt->get_result();
if ($resAmistad->num_rows > 0) {
  $estado_amistad = $resAmistad->fetch_assoc()['estado'];
}

function obtenerLikes($conn, $publicacion_id) {
  $likes = [];
  $res = $conn->query("SELECT u.usuario FROM likes l JOIN usuarios u ON l.usuario_id = u.id WHERE l.publicacion_id = $publicacion_id");
  while ($row = $res->fetch_assoc()) {
    $likes[] = $row['usuario'];
  }
  return $likes;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil de <?= htmlspecialchars($perfil['usuario']) ?> | NEXUS</title>
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

<header class="navbar">
  <div class="logo">NEXUS</div>
  <input type="text" placeholder="Buscar en NEXUS..." class="search-box">
  <div class="nav-icons">
    <a href="index.php">🏠</a>
    <a href="perfil.php">👥</a>
    <a href="#">📈</a>
    <a href="#">🔔</a>
    <a href="#">⚙️</a>
  </div>
  <div class="user-info">
    👋 Hola, <?= htmlspecialchars($usuario_nombre) ?> <a href="logout.php">Cerrar sesión</a>
  </div>
</header>

<div class="container perfil-publico">
  <main class="main-feed">
    <div class="perfil-encabezado">
      <img src="uploads/<?= htmlspecialchars($perfil['foto_perfil'] ?? 'default.jpg') ?>" alt="Foto de perfil" class="foto-perfil-grande">
      <h2><?= htmlspecialchars($perfil['usuario']) ?></h2>
      <p>📍 Vive en <?= htmlspecialchars($perfil['ciudad'] ?? 'No especificado') ?></p>
      <p>🎓 Estudió en <?= htmlspecialchars($perfil['educacion'] ?? 'Desconocido') ?></p>

      <!-- Botón de amistad -->
      <?php if ($id_usuario !== $usuario_logueado): ?>
        <?php if ($estado_amistad === null): ?>
         <form action="enviar_solicitud.php" method="POST">
  <input type="hidden" name="para_usuario_id" value="<?= $perfil['id'] ?>">
  <button type="submit">Agregar como amigo</button>
</form>

        <?php elseif ($estado_amistad === 'pendiente'): ?>
          <p><em>Solicitud enviada</em></p>
        <?php elseif ($estado_amistad === 'aceptada'): ?>
          <p><strong>✔ Amigos</strong></p>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <h3>Publicaciones</h3>
    <?php while ($post = $posts->fetch_assoc()): ?>
      <div class="post">
        <p><?= nl2br(htmlspecialchars($post['contenido'])) ?></p>

        <?php if (!empty($post['imagen'])): ?>
          <?php
            $ext = strtolower(pathinfo($post['imagen'], PATHINFO_EXTENSION));
            $ruta = 'uploads/' . $post['imagen'];

            if (file_exists($ruta)) {
              if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                <img src="<?= $ruta ?>" alt="Imagen del post">
              <?php elseif (in_array($ext, ['mp4', 'webm', 'ogg'])): ?>
                <video controls>
                  <source src="<?= $ruta ?>" type="video/<?= $ext ?>">
                </video>
              <?php endif;
            } else {
              echo "<p style='color:red;'>Archivo no encontrado: {$post['imagen']}</p>";
            }
          ?>
        <?php endif; ?><br>

        <small>Publicado el <?= $post['creado_en'] ?></small>

        <div class="acciones-post">
          <form action="like.php" method="POST" style="display:inline;">
            <input type="hidden" name="publicacion_id" value="<?= $post['id'] ?>">
            <button type="submit" style="background:none;border:none;cursor:pointer;">
              👍 <small>Me gusta</small>
            </button>
          </form>
        </div>

        <?php 
          $usuarios_like = obtenerLikes($conn, $post['id']);
          if (count($usuarios_like) > 0):
        ?>
          <div style="font-size: 13px; color: #555; margin-top: 5px;">
            A <strong><?= implode(', ', array_map('htmlspecialchars', $usuarios_like)) ?></strong> les gusta esto
          </div>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  </main>
</div>

</body>
</html>
