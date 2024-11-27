<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['user_id'] ?? null;
$recipeId = $data['recipe_id'] ?? null;
$action = $data['action'] ?? ''; // 'add' or 'remove'

if ($userId && $recipeId) {
    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
            $stmt->execute([$userId, $recipeId]);
            echo json_encode(['success' => true, 'message' => 'Recipe added to favorites.']);
        } elseif ($action === 'remove') {
            $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
            $stmt->execute([$userId, $recipeId]);
            echo json_encode(['success' => true, 'message' => 'Recipe removed from favorites.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database query error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User ID and Recipe ID are required.']);
}
?>
