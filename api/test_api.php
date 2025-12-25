<?php
// Test the GroceryPlus API

echo "=== GroceryPlus API Test ===\n\n";

// Test 1: Get products
echo "1. Testing GET /api/products\n";
$ch = curl_init('http://localhost/groceryplus/api/products');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "Products found: " . count($data['products']) . "\n";
} else {
    echo "Error: $response\n";
}
echo "\n";

// Test 2: Get categories
echo "2. Testing GET /api/categories\n";
$ch = curl_init('http://localhost/groceryplus/api/categories');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "Categories found: " . count($data) . "\n";
} else {
    echo "Error: $response\n";
}
echo "\n";

// Test 3: Authentication
echo "3. Testing POST /api/auth (login)\n";
$ch = curl_init('http://localhost/groceryplus/api/auth');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'admin@groceryplus.com',
    'password' => 'admin123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "Login successful: " . ($data['token'] ? 'Yes' : 'No') . "\n";
} else {
    echo "Error: $response\n";
}
echo "\n";

echo "=== API Test Complete ===";
?>