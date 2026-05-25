<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

include 'includes/db.php';

$usuario_id = $_SESSION['usuario_id'];
$publicacion_id = isset($_POST['publicacion_id']) ? (int) $_POST['publicacion_id'] : 0;

if ($publicacion_id <= 0) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

// Verificar si ya dio like
$stmt = $conn->prepare("SELECT id FROM likes WHERE publicacion_id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $publicacion_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Eliminar like
    $stmt = $conn->prepare("DELETE FROM likes WHERE publicacion_id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $publicacion_id, $usuario_id);
    $stmt->execute();
    $accion = 'removido';
} else {
    // Agregar like
    $stmt = $conn->prepare("INSERT INTO likes (publicacion_id, usuario_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $publicacion_id, $usuario_id);
    $stmt->execute();
    $accion = 'agregado';
}

// Obtener cantidad total de likes
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE publicacion_id = ?");
$stmt->bind_param("i", $publicacion_id);
$stmt->execute();
$result = $stmt->get_result();
$total = $result->fetch_assoc()['total'];

echo json_encode([
    'accion' => $accion,
    'total_likes' => $total
]);
