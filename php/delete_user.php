<?php
require_once '../config/db_config.php';  // Incluir configuración de la base de datos
require_once 'user_model.php';  // Incluir modelo de usuario

session_start();  // Iniciar sesión para verificar la autenticación y el rol

// Crear una instancia del modelo de usuario con la conexión de la base de datos
$userModel = new User($connection);

// Verificar si el usuario está autenticado y tiene rol de admin
if (!isset($_SESSION['user_id']) || !$userModel->isAdmin($_SESSION['user_id'])) {
    $_SESSION['message'] = "Acceso denegado. Solo los administradores pueden realizar esta acción.";
    header('Location: admin.php');
    exit;
}

// Verificar si se ha enviado un ID de usuario a eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // Obtener el rol del usuario
    $userRole = $userModel->getUserRole($userId);

    if ($userRole === 'user') {
        // Eliminar solo el usuario
        if ($userModel->deleteUser($userId)) {
            $_SESSION['message'] = "Usuario eliminado correctamente.";
        } else {
            $_SESSION['message'] = "Error al eliminar el usuario.";
        }
    } elseif ($userRole === 'publisher') {
        // Eliminar el usuario y sus lugares publicados
        if ($userModel->deleteUserAndPlaces($userId)) {
            $_SESSION['message'] = "Usuario y sus lugares eliminados correctamente.";
        } else {
            $_SESSION['message'] = "Error al eliminar el usuario y sus lugares.";
        }
    } else {
        $_SESSION['message'] = "Error: Rol no reconocido.";
    }
} else {
    $_SESSION['message'] = "ID de usuario no proporcionado.";
}

// Redirigir a admin.php con un mensaje de sesión
header('Location: admin.php');
exit;
?>
