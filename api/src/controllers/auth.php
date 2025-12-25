<?php
// api/src/controllers/auth.php

class AuthController {
    public static function handleAuth($method, $pdo) {
        if ($method !== 'POST') {
            sendError('Method not allowed', 405);
        }

        $data = getJsonInput();
        $errors = [];

        // Validate input
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!validateEmail($data['email'])) {
            $errors['email'] = 'Invalid email format';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }

        if (!empty($errors)) {
            sendError('Validation failed', 422, $errors);
        }

        $email = sanitizeString($data['email']);
        $password = $data['password'];

        // Fetch user from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_email = ? AND user_type = 'customer'");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if (!$user || !password_verify($password, $user['user_password'] ?? '')) {
            sendError('Invalid email or password', 401);
        }

        // Generate token
        $token = generateToken($user['user_id'], false);

        sendResponse([
            'user' => [
                'id' => (int)$user['user_id'],
                'name' => $user['user_name'],
                'email' => $user['user_email'],
                'phone' => $user['user_phone'],
                'type' => 'customer'
            ],
            'token' => $token
        ], 200);
    }
}
