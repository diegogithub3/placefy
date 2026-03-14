<?php
require_once '../config/db_config.php'; // Configuración de la base de datos

session_start();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$publisherId = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$placeId = isset($data['id']) ? $data['id'] : null;

if (!$placeId) {
    echo json_encode(['success' => false, 'message' => 'ID del lugar no proporcionado']);
    exit;
}

$sql = "DELETE FROM places WHERE id = ? AND publisher_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $placeId, $publisherId);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Lugar eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lugar no encontrado o no autorizado para eliminar.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el lugar: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta: ' . $conn->error]);
}

$conn->close();
?>
