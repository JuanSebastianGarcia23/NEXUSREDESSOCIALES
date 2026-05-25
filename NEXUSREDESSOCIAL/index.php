<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];
$res = $conn->query("SELECT usuario FROM usuarios WHERE id = $usuario_id");
$usuario = ($res->num_rows === 1) ? $res->fetch_assoc()['usuario'] : 'Usuario';

// Obtener publicaciones con nombre de usuario y su ID
$posts = $conn->query("SELECT p.*, p.usuario_id, u.usuario FROM publicaciones p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.creado_en DESC");

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
  <title>NEXUS</title>
  <link rel="stylesheet" href="css/index.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

  <header class="navbar">
    <div class="logo">NEXUS</div>
    <input type="text" placeholder="Buscar en NEXUS..." class="search-box">
    <div class="nav-icons">
      <a href="index.php">🏠</a>
      <a href="perfil.php">👥</a>
      <a href="mis_amigos.php">🧑‍🤝‍🧑 Amigos</a>
      <a href="#">📈</a>
      <a href="#">🔔</a>
      <a href="#">⚙️</a>
    </div>
    <div class="user-info">
      👋 Hola, <?= htmlspecialchars($usuario) ?> <a href="logout.php">Cerrar sesión</a>
    </div>
  </header>

  <div class="container">
    <aside class="sidebar-left">
      <a href="#">Inicio</a>
      <a href="#">Amigos</a>
      <a href="#">Grupos</a>
      <a href="#">Marketplace</a>
      <a href="#">Reels</a>
      <a href="#">Configuración</a>
    </aside>

    <main class="main-feed">
      <!-- Formulario para publicar -->
      <form class="post-form" action="publicar.php" method="POST" enctype="multipart/form-data">
        <textarea name="contenido" placeholder="¿Qué estás pensando, <?= htmlspecialchars($usuario) ?>?" required></textarea>
        <input type="file" name="imagen">
        <button type="submit">Publicar</button>
      </form>

      <div class="feed">
        <?php while ($post = $posts->fetch_assoc()): ?>
          <div class="post">
            <!-- Enlace al perfil público -->
            <strong>
              <a href="perfil_publico.php?id=<?= $post['usuario_id'] ?>">
                <?= htmlspecialchars($post['usuario']) ?>
              </a>
            </strong>

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
                      Tu navegador no soporta el video.
                    </video>
                  <?php endif;
                } else {
                  echo "<p style='color:red;'>Archivo no encontrado: {$post['imagen']}</p>";
                }
              ?>
            <?php endif; ?><br>

            <small>Publicado el <?= $post['creado_en'] ?></small>

            <!-- Acciones estilo Facebook -->
            <div class="acciones-post">
              <div class="accion">
                  <button 
                   onclick="darLike(<?= $post['id'] ?>)" 
                   style="background:none;border:none;cursor:pointer;">
                    👍 <small>Me gusta</small>
                  </button>
                  <span id="likes-<?= $post['id'] ?>">
                   <?= $post['total_likes'] ?? 0 ?> Me gusta
                </span>
              </div>
              <div class="accion">
                <span>💬</span>
                <small>Comentar</small>
              </div>
              <div class="accion" onclick="compartir()">
                <span>↗️</span>
                <small>Compartir</small>
              </div>
            </div>

            <?php 
              $usuarios_like = obtenerLikes($conn, $post['id']);
              if (count($usuarios_like) > 0):
            ?>
              <div style="font-size: 13px; color: #555; margin-top: 5px;">
                A <strong><?= implode(', ', array_map('htmlspecialchars', $usuarios_like)) ?></strong> les gusta esto
              </div>
            <?php endif; ?>

            <!-- Comentarios -->
            <div class="comentarios">
              <form class="comentario-form">
                <input type="text" placeholder="Comentar como <?= htmlspecialchars($usuario) ?>...">
              </form>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </main>

    <aside class="sidebar-right">
      <h3>Contactos</h3>
      <ul>
        <li>🟢 Meta AI</li>
        <li>🟢 Stefania</li>
        <li>🟢 Paula</li>
        <li>🟢 Karol</li>
      </ul>
    </aside>
  </div>

  <script>
    function compartir() {
      navigator.clipboard.writeText(window.location.href).then(() => {
        alert("¡Enlace copiado al portapapeles!");
      });
    }
  </script>
<script>
function darLike(publicacionId) {

  fetch('like.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'publicacion_id=' + publicacionId
  })
  .then(response => response.json())
  .then(data => {

    if (data.error) {
      alert(data.error);
      return;
    }

    document.getElementById("likes-" + publicacionId).innerText =
      data.total_likes + " Me gusta";

  })
  .catch(error => console.error("Error:", error));
}
</script>
</body>
</html>

