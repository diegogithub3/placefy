<?php
require_once '../config/db_config.php'; // Configuración de la base de datos

session_start();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$currentLat = isset($_GET['lat']) ? (float)$_GET['lat'] : null;
$currentLng = isset($_GET['lng']) ? (float)$_GET['lng'] : null;
$searchOption = isset($_GET['searchOption']) ? $_GET['searchOption'] : 'distance';
$maxDistance = isset($_GET['distance']) ? (float)$_GET['distance'] : 5; // Valor por defecto si no se ingresa distancia

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

// Generar la consulta SQL
$sql = "SELECT id, name, address, description, latitude, longitude FROM places WHERE name LIKE ?";
$stmt = $conn->prepare($sql);
$searchQuery = '%' . $query . '%';
$stmt->bind_param("s", $searchQuery);
$stmt->execute();
$result = $stmt->get_result();
$places = $result->fetch_all(MYSQLI_ASSOC);

$filteredPlaces = [];

foreach ($places as $place) {
    if ($currentLat !== null && $currentLng !== null) {
        $distance = calculateDistance($currentLat, $currentLng, $place['latitude'], $place['longitude']);
        if ($distance <= $maxDistance) {
            $place['distance'] = round($distance, 1); // Redondear a 1 decimal
            $filteredPlaces[] = $place;
        }
    } else {
        // Si no se proporciona ubicación actual, incluir el lugar sin considerar la distancia
        $place['distance'] = 'N/A';
        $filteredPlaces[] = $place;
    }
}

// Filtrar resultados según el filtro de distancia o calificación
if ($searchOption === 'rating') {
    // Ordenar por calificación (esto solo se aplica si tienes calificaciones en la base de datos)
    usort($filteredPlaces, function($a, $b) {
        return $b['rating'] <=> $a['rating']; // Ordenar por calificación de mayor a menor
    });
} else if ($searchOption === 'distance' && $currentLat !== null && $currentLng !== null) {
    usort($filteredPlaces, function($a, $b) use ($currentLat, $currentLng) {
        $distA = calculateDistance($currentLat, $currentLng, $a['latitude'], $a['longitude']);
        $distB = calculateDistance($currentLat, $currentLng, $b['latitude'], $b['longitude']);
        return $distA <=> $distB; // Ordenar por distancia de menor a mayor
    });
}

$filteredPlaces = array_slice($filteredPlaces, 0, 3); // Limitar a 3 resultados

echo json_encode(['places' => $filteredPlaces]);

$stmt->close();
$conn->close();
?>
