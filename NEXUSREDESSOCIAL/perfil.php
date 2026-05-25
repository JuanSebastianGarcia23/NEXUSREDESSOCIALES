<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];
$res = $conn->query("SELECT * FROM usuarios WHERE id = $usuario_id");
$usuario = ($res->num_rows === 1) ? $res->fetch_assoc() : null;

if (!$usuario) {
  echo "Error al cargar el perfil.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil | NEXUS</title>
  <link rel="stylesheet" href="css/perfil.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

  <header class="navbar">
    <div class="logo">NEXUS</div>
    <input type="text" placeholder="Buscar en NEXUS..." class="search-box">
    <div class="nav-icons">
      <a href="index.php">🏠</a>
      <a href="#">👥</a>
     <a href="solicitudes.php">👥 Solicitudes</a>
      <a href="#">🔔</a>
      <a href="#">⚙️</a>
    </div>
    <div class="user-info">
      👋 Hola, <?= htmlspecialchars($usuario['usuario']) ?> <a href="logout.php" style="color: white;">Cerrar sesión</a>
    </div>
  </header>

  <div class="container">
    <main class="main-feed">
      <div class="profile-header">
        <?php
          $foto = !empty($usuario['foto']) ? 'uploads/' . htmlspecialchars($usuario['foto']) : 'img/default-profile.png';
        ?>
        <img src="<?= $foto ?>" alt="Foto de perfil" class="profile-pic">
        <h2><?= htmlspecialchars($usuario['usuario']) ?></h2>
        <p>Email: <?= htmlspecialchars($usuario['email']) ?></p>
        <a href="editar_perfil.php" class="edit-profile-btn">Editar perfil</a>
      </div>

      <div class="profile-posts">
        <h3>Mis publicaciones</h3>
        <?php
        $posts = $conn->query("SELECT * FROM publicaciones WHERE usuario_id = $usuario_id ORDER BY creado_en DESC");
        while ($post = $posts->fetch_assoc()):
        ?>
          <div class="post">
            <?php if (!empty($post['contenido'])): ?>
              <p><?= nl2br(htmlspecialchars($post['contenido'])) ?></p>
            <?php endif; ?>

            <?php
            if (!empty($post['imagen'])) {
              $ext = strtolower(pathinfo($post['imagen'], PATHINFO_EXTENSION));
              $ruta = 'uploads/' . htmlspecialchars($post['imagen']);

              if (file_exists($ruta)) {
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                  echo '<img src="' . $ruta . '" alt="Imagen de la publicación">';
                } elseif (in_array($ext, ['mp4', 'webm', 'ogg'])) {
                  echo '<video controls>
                          <source src="' . $ruta . '" type="video/' . $ext . '">
                          Tu navegador no soporta el video.
                        </video>';
                } else {
                  echo '<p>Tipo de archivo no soportado.</p>';
                }
              } else {
                echo '<p style="color: red;">Archivo no encontrado: ' . htmlspecialchars($post['imagen']) . '</p>';
              }
            }
            ?><br>

            <small>Publicado el <?= date('d M Y H:i', strtotime($post['creado_en'])) ?></small>
          </div>
        <?php endwhile; ?>
      </div>
    </main>
  </div>

</body>
</html>
