<?php
require_once '../config/db_config.php';
require_once 'user_model.php';

session_start();

// Inicializar conexión a la base de datos y modelo de usuario
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$userModel = new User($conn);

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['user_id']) || !$userModel->isAdmin($_SESSION['user_id'])) {
    echo "Acceso denegado. Solo los administradores pueden acceder a esta página.";
    exit;
}

// Obtener todos los usuarios
$users = $userModel->getAllUsers();

// Incluir el archivo HTML
include '../views/admin.html';
?>
