<?php
// Test the API by directly accessing the file

echo "=== Testing API Endpoints ===\n\n";

// Test 1: Direct file access
echo "1. Testing direct file access\n";
$result = file_get_contents('http://localhost/groceryplus/api/minimal_test.php');
if ($result) {
    $data = json_decode($result, true);
    echo "✅ Success: " . $data['status'] . "\n";
} else {
    echo "❌ Failed: Could not access API\n";
}
echo "\n";

// Test 2: Try accessing API index directly
echo "2. Testing API index.php directly\n";
$result = file_get_contents('http://localhost/groceryplus/api/index.php/products');
if ($result) {
    echo "✅ API routing works\n";
    echo "Response: " . substr($result, 0, 100) . "...\n";
} else {
    echo "❌ API routing failed\n";
}
echo "\n";

echo "=== Test Complete ===";
?>