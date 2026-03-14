<?php
require_once '../config/db_config.php'; // Configuración de la base de datos
require_once 'user_model.php'; // Modelo de usuario

session_start();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$userModel = new User($conn);

if (!isset($_SESSION['user_id']) || !$userModel->isPublisher($_SESSION['user_id'])) {
    echo "Acceso denegado. Solo los publishers pueden publicar lugares.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['placeName'];
    $address = $_POST['address'];
    $description = $_POST['description'];
    $publisherId = $_SESSION['user_id'];
    $latitude = $_POST['latitude']; // Recibido del frontend
    $longitude = $_POST['longitude']; // Recibido del frontend   

    $sql = "INSERT INTO places (publisher_id, name, address, description, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isssdd", $publisherId, $name, $address, $description, $latitude, $longitude);
        if ($stmt->execute()) {
            echo "Lugar publicado correctamente.";
        } else {
            echo "Error al publicar el lugar: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $conn->error;
    }

    $conn->close();
}
?>
