<?php

class UploadController {
    public static function handleUpload($method) {
        if ($method !== 'POST') {
            sendError('Method not allowed', 405);
        }

        if (!validateToken()) {
            sendError('Unauthorized', 401);
        }

        $user = getUserFromToken();
        if (!$user || $user['type'] !== 'admin') {
            sendError('Admin access required', 403);
        }

        if (!isset($_FILES['image'])) {
            sendError('No image file provided', 400);
        }

        $file = $_FILES['image'];

        // Validate file type
        if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            sendError('Invalid file type. Only JPEG, PNG, GIF, and WebP allowed', 422);
        }

        // Validate file size
        if ($file['size'] > MAX_FILE_SIZE) {
            sendError('File too large. Maximum size is 5MB', 422);
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            error_log("File upload error: " . $file['error']);
            sendError('Upload failed. Please try again.', 500);
        }

        // Create upload directory if doesn't exist
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        // Generate unique filename
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid('img_', true) . '.' . $ext;
        $uploadPath = UPLOAD_DIR . $fileName;

        // Validate file can be read as image
        if (!getimagesize($file['tmp_name'])) {
            sendError('Invalid image file', 422);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            error_log("Failed to move uploaded file: $uploadPath");
            sendError('Upload failed. Please try again.', 500);
        }

        // Set proper permissions
        chmod($uploadPath, 0644);

        sendResponse([
            'success' => true,
            'image_url' => API_BASE_URL . "/images/$fileName",
            'filename' => $fileName,
            'size' => filesize($uploadPath)
        ], 200);
    }
}
?>