<?php
// api/src/controllers/register.php

class RegisterController {
    public static function handleRegister($method, $pdo) {
        if ($method !== 'POST') {
            sendError('Method not allowed', 405);
        }

        $data = getJsonInput();
        $errors = [];

        // Validate inputs
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Name must be at least 2 characters';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!validateEmail($data['email'])) {
            $errors['email'] = 'Invalid email format';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (!validatePassword($data['password'])) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone is required';
        } elseif (!validatePhone($data['phone'])) {
            $errors['phone'] = 'Invalid phone format';
        }

        if (!empty($errors)) {
            sendError('Validation failed', 422, $errors);
        }

        $name = sanitizeString($data['name']);
        $email = sanitizeString($data['email']);
        $phone = sanitizeString($data['phone']);
        $password = $data['password'];

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE user_email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            sendError('Email already registered', 409);
        }

        // Hash password with BCRYPT
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO users (user_name, user_email, user_phone, user_password, user_type, created_at)
            VALUES (?, ?, ?, ?, 'customer', CURRENT_TIMESTAMP)
        ");
        
        try {
            $stmt->execute([$name, $email, $phone, $hashedPassword]);
            $userId = $pdo->lastInsertId();

            // Generate token
            $token = generateToken($userId, false);

            sendResponse([
                'user' => [
                    'id' => (int)$userId,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'type' => 'customer'
                ],
                'token' => $token,
                'message' => 'Registration successful'
            ], 201);
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            sendError('Registration failed. Please try again.', 500);
        }
    }
}
