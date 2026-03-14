<?php
require_once '../config/db_config.php'; // Configuración de la base de datos
require_once 'user_model.php'; // Modelo de usuario

session_start(); // Iniciar sesión

// Usar la conexión existente en db_config.php
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$userModel = new User($conn); // Crear una instancia del modelo de usuario

// Función para obtener el nombre de usuario
function getUsername($userId) {
    global $userModel;
    return $userModel->getUsernameById($userId);
}

// Verificar si la solicitud es del tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se han enviado el nombre de usuario y la contraseña
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Autenticar al usuario
        $user = $userModel->authenticate($username, $password);

        // Verificar si las credenciales son correctas
        if ($user) {
            // Guardar información del usuario en la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // Guardar el nombre de usuario en la sesión
            $_SESSION['role'] = $user['role'];

            // Establecer la cookie para el nombre de usuario
            setcookie('username', $user['username'], time() + 3600, '/'); // Cookie válida por 1 hora

            // Redirigir según el rol del usuario
            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else if ($user['role'] === 'user') {
                header('Location: ../views/u.html'); // Redirigir a la página de bienvenida del usuario
            } else if ($user['role'] === 'publisher') {
                header('Location: ../views/p.html'); // Redirigir a la página de bienvenida del usuario
            }
            exit; // Terminar el script después de la redirección
        } else {
            // Manejo de error: credenciales incorrectas
            $_SESSION['login_error'] = "Credenciales incorrectas.";
            header('Location: login.html'); // Redirigir a la página de inicio de sesión con mensaje de error
            exit;
        }
    } else {
        // Redirigir a la página de inicio de sesión si no se han enviado los datos
        header('Location: login.html');
        exit;
    }
} else {
    // Redirigir a la página de inicio de sesión si el método no es POST
    header('Location: login.html');
    exit;
}
?>
