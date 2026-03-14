<?php
require_once '../config/db_config.php'; // Configuración de la base de datos
require_once 'user_model.php'; // Modelo de usuario

session_start();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$userModel = new User($conn);

if (!isset($_SESSION['user_id']) || !$userModel->isPublisher($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Acceso denegado.']);
    exit;
}

$userId = $_SESSION['user_id'];
$currentLat = isset($_GET['lat']) ? (float)$_GET['lat'] : null;
$currentLng = isset($_GET['lng']) ? (float)$_GET['lng'] : null;

function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $R = 6371; // Radio de la Tierra en km
    $dLat = ($lat2 - $lat1) * M_PI / 180;
    $dLng = ($lng2 - $lng1) * M_PI / 180;
    $a = 
        0.5 - cos($dLat) / 2 + 
        cos($lat1 * M_PI / 180) * cos($lat2 * M_PI / 180) * 
        (1 - cos($dLng)) / 2;

    return $R * 2 * asin(sqrt($a));
}

$sql = "SELECT id, name, address, description, latitude, longitude FROM places WHERE publisher_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $places = $result->fetch_all(MYSQLI_ASSOC);
    
    foreach ($places as &$place) {
        if ($currentLat !== null && $currentLng !== null) {
            $distance = calculateDistance($currentLat, $currentLng, $place['latitude'], $place['longitude']);
            $place['distance'] = round($distance, 1); // Redondear a 1 decimal
        } else {
            $place['distance'] = 'N/A'; // Si no se proporciona ubicación actual, distancia es N/A
        }
    }

    echo json_encode(['places' => $places]);
    $stmt->close();
} else {
    echo json_encode(['error' => 'Error en la preparación de la consulta.']);
}

$conn->close();
?>
