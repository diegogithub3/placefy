<?php
include '../php/db_config.php'; // Incluye el archivo de configuración

// Verifica si la conexión se ha establecido correctamente
if (!$connection) {
    die('Error de conexión: ' . mysqli_connect_error());
}

// Prepara la consulta para insertar el usuario
$username = 'admin';
$email = 'admin@example.com';
$password = password_hash('admin', PASSWORD_BCRYPT); // Hash de la contraseña
$role = 'admin';

$stmt = $connection->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $username, $email, $password, $role);

if ($stmt->execute()) {
    echo "Nuevo registro creado con éxito";
} else {
    echo "Error: " . $stmt->error;
}

// Cierra la conexión
$stmt->close();
$connection->close();
?>
