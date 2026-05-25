<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];

// Obtener nombre del usuario
$res = $conn->query("SELECT usuario FROM usuarios WHERE id = $usuario_id");
$usuario = ($res->num_rows === 1) ? $res->fetch_assoc()['usuario'] : 'Usuario';

// Consulta para obtener amigos aceptados
$sql = "
  SELECT u.id, u.usuario, u.foto_perfil
  FROM amistades a
  JOIN usuarios u ON (u.id = a.solicitante_id OR u.id = a.receptor_id)
  WHERE a.estado = 'aceptada'
    AND (a.solicitante_id = $usuario_id OR a.receptor_id = $usuario_id)
    AND u.id != $usuario_id
";

$amigos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis amigos | NEXUS</title>
  <link rel="stylesheet" href="css/perfil.css">
  <style>
    body {
      font-family: sans-serif;
      background: #f5f7fa;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      padding: 20px;
      text-align: center;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #fff;
      padding: 10px 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .navbar a {
      margin-right: 10px;
      text-decoration: none;
      color: #333;
      font-weight: bold;
    }

    .logo {
      font-size: 20px;
      font-weight: bold;
    }

    h2 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .lista-amigos {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
      margin-top: 20px;
    }

    .amigo {
      display: flex;
      flex-direction: column;
      align-items: center;
      background: #ffffff;
      padding: 15px;
      border-radius: 10px;
      width: 100%;
      max-width: 300px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .amigo img {
      border-radius: 50%;
      width: 80px;
      height: 80px;
      object-fit: cover;
      margin-bottom: 10px;
    }

    .amigo a {
      font-weight: bold;
      font-size: 18px;
      text-decoration: none;
      color: #333;
    }

    .amigo a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <header class="navbar">
    <div class="logo">NEXUS</div>
    <div>
      <a href="index.php">🏠 Inicio</a>
      <a href="perfil.php">👤 Perfil</a>
      <a href="solicitudes.php">👥 Solicitudes</a>
      <a href="mis_amigos.php">🧑‍🤝‍🧑 Amigos</a>
    </div>
    <div class="user-info">
      Hola, <?= htmlspecialchars($usuario) ?> | <a href="logout.php">Cerrar sesión</a>
    </div>
  </header>

  <h2>Lista de amigos</h2>
<br>

    
    <div class="lista-amigos">
      <?php if ($amigos->num_rows > 0): ?>
        <?php while ($amigo = $amigos->fetch_assoc()): ?>
          <div class="amigo">
            <img src="uploads/<?= (isset($amigo['foto_perfil']) && file_exists('uploads/' . $amigo['foto_perfil'])) ? htmlspecialchars($amigo['foto_perfil']) : 'default-profile.png' ?>" alt="Foto de perfil">
            <a href="perfil_publico.php?id=<?= $amigo['id'] ?>">
              <?= htmlspecialchars($amigo['usuario']) ?>
            </a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No tienes amigos aún 😔</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>


