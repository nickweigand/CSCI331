<?php
require 'config.php';

session_start();
$token = $_COOKIE['session_token'] ?? null;

if ($token) {
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE session_token = ?");
    $stmt->execute([$token]);
    setcookie("session_token", "", time() - 3600, "/");
}

echo json_encode(['success' => true]);
?>
