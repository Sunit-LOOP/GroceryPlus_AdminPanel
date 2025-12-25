<?php
// api/src/controllers/categories.php

class CategoryController {
    public static function handleCategories($method, $id, $pdo) {
        // Admin access check for POST, PUT, DELETE
        if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
            if (!validateToken()) sendError('Unauthorized', 401);
            $user = getUserFromToken();
            if (!$user || $user['type'] !== 'admin') {
                sendError('Admin access required', 403);
            }
        }

        switch ($method) {
            case 'GET':
                if ($id) {
                    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
                    $stmt->execute([(int)$id]);
                    $category = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($category) {
                        sendResponse($category);
                    } else {
                        sendError('Category not found', 404);
                    }
                } else {
                    $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name");
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;

            case 'POST':
                $data = getJsonInput();
                $errors = [];
                if (empty($data['category_name'])) {
                    $errors['category_name'] = 'Category name is required';
                }
                if (!empty($errors)) {
                    sendError('Validation failed', 422, $errors);
                }
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO categories (category_name, category_description) VALUES (?, ?)");
                    $stmt->execute([sanitizeString($data['category_name']), sanitizeString($data['category_description'] ?? '')]);
                    sendResponse(['category_id' => (int)$pdo->lastInsertId(), 'message' => 'Category created successfully'], 201);
                } catch (PDOException $e) {
                    error_log("Category creation error: " . $e->getMessage());
                    sendError('Failed to create category', 500);
                }
                break;

            case 'PUT':
                if (!$id) sendError('Category ID required', 400);
                
                $data = getJsonInput();
                $errors = [];
                if (empty($data['category_name'])) {
                    $errors['category_name'] = 'Category name is required';
                }
                if (!empty($errors)) {
                    sendError('Validation failed', 422, $errors);
                }

                try {
                    $stmt = $pdo->prepare("UPDATE categories SET category_name = ?, category_description = ? WHERE category_id = ?");
                    $stmt->execute([
                        sanitizeString($data['category_name']),
                        sanitizeString($data['category_description'] ?? ''),
                        (int)$id
                    ]);
                    sendResponse(['success' => true, 'message' => 'Category updated successfully']);
                } catch (PDOException $e) {
                    error_log("Category update error: " . $e->getMessage());
                    sendError('Failed to update category', 500);
                }
                break;

            case 'DELETE':
                if (!$id) sendError('Category ID required', 400);
                
                try {
                    $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
                    $stmt->execute([(int)$id]);
                    if ($stmt->rowCount() > 0) {
                        sendResponse(['success' => true, 'message' => 'Category deleted successfully']);
                    } else {
                        sendError('Category not found', 404);
                    }
                } catch (PDOException $e) {
                    error_log("Category deletion error: " . $e->getMessage());
                    sendError('Failed to delete category', 500);
                }
                break;

            default:
                sendError('Method not allowed', 405);
        }
    }
}
