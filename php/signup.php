<?php
require_once '../config/db_config.php'; // Asegúrate de que este archivo esté configurado correctamente para mysqli

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar la contraseña
    $role = $_POST['role']; // Capturar el rol del usuario

    // Crear la conexión
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Preparar la consulta
    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        if ($stmt->execute()) {
            // Mostrar alerta y redirigir a login.html
            echo "<script>
                    alert('Registro exitoso!');
                    window.location.href = '../views/login.html';
                  </script>";
        } else {
            echo "Error al registrar el usuario: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
