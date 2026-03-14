<?php
require_once '../config/db_config.php';  // Incluir configuración de la base de datos
require_once 'user_model.php'; // Incluir modelo de usuario

session_start(); // Iniciar sesión para verificar la autenticación y el rol

// Crear una instancia del modelo de usuario con la conexión de la base de datos
$userModel = new User($connection);  // Cambiar $conn por $connection

if (!isset($_SESSION['user_id']) || !$userModel->isAdmin($_SESSION['user_id'])) {
    echo "Acceso denegado. Solo los administradores pueden realizar esta acción.";
    exit;
}

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $user = $userModel->getUserById($userId);

    if (!$user) {
        echo "Usuario no encontrado.";
        exit;
    }
} else {
    echo "ID de usuario no proporcionado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($userModel->updateUser($userId, $username, $email, $role)) {
        $_SESSION['message'] = "Usuario actualizado correctamente.";
        header('Location: admin.php');
        exit;
    } else {
        $_SESSION['message'] = "Error al actualizar el usuario.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/edit_user.css">
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form action="edit_user.php?id=<?php echo htmlspecialchars($userId); ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <br>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                <option value="publisher" <?php echo ($user['role'] === 'publisher') ? 'selected' : ''; ?>>Publisher</option>
            </select>
            <br>
            <button type="submit">Update User</button>
        </form>
        <a href="admin.php" class="back-to-admin">Back to Admin Panel</a>
    </div>
</body>
</html>
