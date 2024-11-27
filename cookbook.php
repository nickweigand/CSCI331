<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['user_id'] ?? null;
$action = $data['action'] ?? ''; // 'create', 'add_recipe', or 'remove_recipe'

if ($userId) {
    try {
        if ($action === 'create') {
            // Create a new cookbook
            $name = $data['name'] ?? 'Untitled Cookbook';
            $stmt = $pdo->prepare("INSERT INTO cookbooks (name, user_id) VALUES (?, ?)");
            $stmt->execute([$name, $userId]);
            echo json_encode(['success' => true, 'message' => 'Cookbook created.']);
        } elseif ($action === 'add_recipe') {
            // Add a recipe to the user's cookbook
            $cookbookId = $data['cookbook_id'] ?? null;
            $recipeName = $data['recipe_name'] ?? null;
            $recipeDetails = $data['recipe_details'] ?? null;

            if ($cookbookId && $recipeName && $recipeDetails) {
                $stmt = $pdo->prepare("INSERT INTO cookbook_recipes (cookbook_id, recipe_name, recipe_details) VALUES (?, ?, ?)");
                $stmt->execute([$cookbookId, $recipeName, $recipeDetails]);
                echo json_encode(['success' => true, 'message' => 'Recipe added to cookbook.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cookbook ID, Recipe Name, and Recipe Details are required.']);
            }
        } elseif ($action === 'remove_recipe') {
            // Remove a recipe from the user's cookbook
            $cookbookId = $data['cookbook_id'] ?? null;
            $recipeId = $data['recipe_id'] ?? null;

            if ($cookbookId && $recipeId) {
                $stmt = $pdo->prepare("DELETE FROM cookbook_recipes WHERE cookbook_id = ? AND id = ?");
                $stmt->execute([$cookbookId, $recipeId]);
                echo json_encode(['success' => true, 'message' => 'Recipe removed from cookbook.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cookbook ID and Recipe ID are required.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database query error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User ID is required.']);
}
?>
