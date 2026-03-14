<?php

require_once '../config/db_config.php'; // Configuración de conexión a la base de datos

class User {
    private $conn;

    // Constructor para inicializar la conexión a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para registrar un nuevo usuario
    public function register($username, $email, $password, $role) {
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
        return $stmt->execute();
    }

    // Método para verificar las credenciales del usuario durante el inicio de sesión
    public function authenticate($username, $password) {
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }

    // Método para obtener información del usuario por ID
    public function getUserById($userId) {
        $sql = "SELECT id, username, email, role FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Método para validar si un usuario es admin
    public function isAdmin($userId) {
        $user = $this->getUserById($userId);
        return $user && $user['role'] === 'admin';
    }

    // Método para verificar si un usuario es publisher
    public function isPublisher($userId) {
        $user = $this->getUserById($userId);
        return $user && $user['role'] === 'publisher';
    }

    // Método para listar todos los usuarios (solo para admin)
    public function getAllUsers() {
        $sql = "SELECT id, username, email, role FROM users";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Método para eliminar un usuario por ID (solo para admin)
    public function deleteUser($userId) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    // Método para editar datos de un usuario
    public function updateUser($userId, $username, $email, $role) {
        $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }
    
        $stmt->bind_param("sssi", $username, $email, $role, $userId);
        return $stmt->execute();
    }

    // Método para obtener el nombre de usuario por ID
    public function getUsernameById($userId) {
        $sql = "SELECT username FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user ? $user['username'] : false;
    }

    // Método para obtener el rol del usuario por ID
    public function getUserRole($userId) {
        $sql = "SELECT role FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['role'];
        } else {
            return null;
        }
    }

    // Método para eliminar un usuario y sus lugares si es publisher
    public function deleteUserAndPlaces($userId) {
        // Iniciar transacción
        $this->conn->begin_transaction();
        
        try {
            // Eliminar lugares asociados al publisher
            $sqlPlaces = "DELETE FROM places WHERE publisher_id = ?";
            $stmtPlaces = $this->conn->prepare($sqlPlaces);
            if ($stmtPlaces === false) {
                throw new Exception("Error al preparar la eliminación de lugares.");
            }
            $stmtPlaces->bind_param("i", $userId);
            $stmtPlaces->execute();

            // Eliminar el usuario
            $sqlUser = "DELETE FROM users WHERE id = ?";
            $stmtUser = $this->conn->prepare($sqlUser);
            if ($stmtUser === false) {
                throw new Exception("Error al preparar la eliminación de usuario.");
            }
            $stmtUser->bind_param("i", $userId);
            $stmtUser->execute();

            // Confirmar transacción
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->conn->rollback();
            return false;
        }
    }
}

?>
