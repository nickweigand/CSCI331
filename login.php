<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if ($email && $password) {
    try {
        // Check if the user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Verify the password for an existing user
            if (password_verify($password, $user['password_hash'])) {
                echo json_encode(['success' => true, 'message' => 'Login successful.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password.']);
            }
        } else {
            // User does not exist, skip further processing
            echo json_encode(['success' => false, 'message' => 'User not found.']);
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['success' => false, 'message' => 'Database query error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
}
?>
