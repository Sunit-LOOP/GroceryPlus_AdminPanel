<?php
/**
 * GroceryPlus API Testing Script
 * 
 * This script tests all API endpoints and can be used for development/debugging
 * Run: php api_test.php
 */

define('API_BASE', 'http://localhost/groceryplus/api');
define('TEST_EMAIL', 'test' . time() . '@example.com');
define('TEST_PASSWORD', 'TestPassword123');

$results = [];
$token = null;
$userId = null;

/**
 * Helper function to make API requests
 */
function apiRequest($endpoint, $method = 'GET', $data = null, $token = null) {
    $url = API_BASE . '/' . $endpoint;
    
    $options = [
        'http' => [
            'method' => $method,
            'header' => [
                'Content-Type: application/json',
            ],
            'timeout' => 30
        ]
    ];
    
    if ($token) {
        $options['http']['header'][] = "Authorization: Bearer $token";
    }
    
    if ($data && in_array($method, ['POST', 'PUT'])) {
        $options['http']['content'] = json_encode($data);
    }
    
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        $httpCode = $http_response_header[0] ?? null;
        
        return [
            'success' => $response !== false,
            'response' => $response ? json_decode($response, true) : null,
            'httpCode' => $httpCode
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Test function wrapper
 */
function test($name, $result, &$results) {
    $status = $result ? '✓ PASS' : '✗ FAIL';
    echo "[$status] $name\n";
    $results[] = ['test' => $name, 'passed' => $result];
    return $result;
}

echo "======================================\n";
echo "GroceryPlus API Test Suite\n";
echo "======================================\n\n";

// Test 1: User Registration
echo "1. USER AUTHENTICATION TESTS\n";
echo "---\n";

$registerResult = apiRequest('register', 'POST', [
    'name' => 'Test User',
    'email' => TEST_EMAIL,
    'password' => TEST_PASSWORD,
    'phone' => '+977-9841234567'
]);

test('Register new user', 
    $registerResult['success'] && 
    isset($registerResult['response']['data']['token']) &&
    isset($registerResult['response']['data']['user']['id']),
    $results
);

if ($registerResult['success'] && isset($registerResult['response']['data'])) {
    $token = $registerResult['response']['data']['token'];
    $userId = $registerResult['response']['data']['user']['id'];
}

// Test 2: User Login
$loginResult = apiRequest('auth', 'POST', [
    'email' => TEST_EMAIL,
    'password' => TEST_PASSWORD
]);

test('Login with valid credentials',
    $loginResult['success'] &&
    isset($loginResult['response']['data']['token']),
    $results
);

// Test 3: Invalid Login
$invalidLoginResult = apiRequest('auth', 'POST', [
    'email' => TEST_EMAIL,
    'password' => 'wrongpassword'
]);

test('Login with invalid password fails',
    !$invalidLoginResult['success'] ||
    strpos($loginResult['response']['data']['token'] ?? '', 'user_') === 0,
    $results
);

echo "\n2. PRODUCT TESTS\n";
echo "---\n";

// Test 4: Get Products
$productsResult = apiRequest('products', 'GET', null, $token);

test('Get all products',
    $productsResult['success'] &&
    isset($productsResult['response']['data']['products']),
    $results
);

// Test 5: Get Product Details
if ($productsResult['success'] && !empty($productsResult['response']['data']['products'])) {
    $firstProduct = $productsResult['response']['data']['products'][0];
    $productId = $firstProduct['product_id'];
    
    $singleProductResult = apiRequest("products/$productId", 'GET', null, $token);
    
    test('Get single product details',
        $singleProductResult['success'] &&
        isset($singleProductResult['response']['data']['product_id']),
        $results
    );
}

// Test 6: Search Products
$searchResult = apiRequest('products?search=apple', 'GET', null, $token);

test('Search products by name',
    $searchResult['success'] &&
    isset($searchResult['response']['data']['products']),
    $results
);

// Test 7: Filter by Category
$categoryResult = apiRequest('products?category=fruits', 'GET', null, $token);

test('Filter products by category',
    $categoryResult['success'] &&
    isset($categoryResult['response']['data']['products']),
    $results
);

echo "\n3. CATEGORY TESTS\n";
echo "---\n";

// Test 8: Get Categories
$categoriesResult = apiRequest('categories', 'GET', null, $token);

test('Get all categories',
    $categoriesResult['success'] &&
    is_array($categoriesResult['response']['data']),
    $results
);

echo "\n4. CART TESTS\n";
echo "---\n";

// Test 9: Get Empty Cart
$cartResult = apiRequest('cart', 'GET', null, $token);

test('Get user cart',
    $cartResult['success'] &&
    isset($cartResult['response']['data']['cart']),
    $results
);

// Test 10: Add to Cart
if (isset($productId)) {
    $addCartResult = apiRequest('cart', 'POST', [
        'product_id' => $productId,
        'quantity' => 2
    ], $token);
    
    test('Add item to cart',
        $addCartResult['success'] &&
        isset($addCartResult['response']['data']['success']),
        $results
    );
}

echo "\n5. ORDER TESTS\n";
echo "---\n";

// Test 11: Get Orders
$ordersResult = apiRequest('orders', 'GET', null, $token);

test('Get user orders',
    $ordersResult['success'] &&
    is_array($ordersResult['response']['data']),
    $results
);

// Test 12: Create Order
if (isset($userId) && isset($productId)) {
    $createOrderResult = apiRequest('orders', 'POST', [
        'user_id' => $userId,
        'delivery_fee' => 100,
        'items' => [
            [
                'product_id' => $productId,
                'quantity' => 1,
                'price' => 150
            ]
        ]
    ], $token);
    
    test('Create new order',
        $createOrderResult['success'] &&
        isset($createOrderResult['response']['data']['order_id']),
        $results
    );
    
    if ($createOrderResult['success']) {
        $orderId = $createOrderResult['response']['data']['order_id'];
        
        // Test 13: Get Order Details
        $orderDetailsResult = apiRequest("orders/$orderId", 'GET', null, $token);
        
        test('Get order details',
            $orderDetailsResult['success'] &&
            isset($orderDetailsResult['response']['data']['order_id']),
            $results
        );
    }
}

echo "\n6. FAVORITES TESTS\n";
echo "---\n";

// Test 14: Get Favorites
$favoritesResult = apiRequest('favorites', 'GET', null, $token);

test('Get user favorites',
    $favoritesResult['success'] &&
    isset($favoritesResult['response']['data']['favorites']),
    $results
);

// Test 15: Add Favorite
if (isset($productId)) {
    $addFavResult = apiRequest('favorites', 'POST', [
        'product_id' => $productId
    ], $token);
    
    test('Add product to favorites',
        $addFavResult['success'],
        $results
    );
}

echo "\n7. REVIEWS TESTS\n";
echo "---\n";

// Test 16: Get Product Reviews
if (isset($productId)) {
    $reviewsResult = apiRequest("reviews/$productId", 'GET', null, $token);
    
    test('Get product reviews',
        $reviewsResult['success'] &&
        is_array($reviewsResult['response']['data']),
        $results
    );
    
    // Test 17: Submit Review
    $submitReviewResult = apiRequest('reviews', 'POST', [
        'product_id' => $productId,
        'rating' => 5,
        'review' => 'Excellent product!'
    ], $token);
    
    test('Submit product review',
        $submitReviewResult['success'],
        $results
    );
}

echo "\n8. MESSAGES TESTS\n";
echo "---\n";

// Test 18: Get Messages
$messagesResult = apiRequest('messages', 'GET', null, $token);

test('Get user messages',
    $messagesResult['success'] &&
    isset($messagesResult['response']['data']['conversations']),
    $results
);

echo "\n9. NOTIFICATIONS TESTS\n";
echo "---\n";

// Test 19: Get Notifications
$notificationsResult = apiRequest('notifications', 'GET', null, $token);

test('Get user notifications',
    $notificationsResult['success'] &&
    is_array($notificationsResult['response']['data']),
    $results
);

echo "\n10. ERROR HANDLING TESTS\n";
echo "---\n";

// Test 20: Missing Token
$noTokenResult = apiRequest('orders', 'GET', null, null);

test('Request without token fails',
    !$noTokenResult['success'] ||
    strpos(json_encode($noTokenResult), 'Unauthorized') !== false,
    $results
);

// Test 21: Invalid Endpoint
$invalidEndpointResult = apiRequest('invalid-endpoint', 'GET', null, $token);

test('Invalid endpoint returns 404',
    !$invalidEndpointResult['success'] ||
    strpos(json_encode($invalidEndpointResult), 'not found') !== false,
    $results
);

// Test 22: Invalid Method
$invalidMethodResult = apiRequest('products', 'DELETE', null, $token);

test('Invalid HTTP method fails',
    !$invalidMethodResult['success'],
    $results
);

// Summary
echo "\n======================================\n";
echo "TEST SUMMARY\n";
echo "======================================\n";

$passed = count(array_filter($results, fn($r) => $r['passed']));
$total = count($results);
$percentage = ($total > 0) ? round(($passed / $total) * 100, 2) : 0;

echo "Total Tests: $total\n";
echo "Passed: $passed\n";
echo "Failed: " . ($total - $passed) . "\n";
echo "Success Rate: $percentage%\n";

if ($passed === $total) {
    echo "\n✓ All tests passed!\n";
} else {
    echo "\n✗ Some tests failed. Review output above.\n";
}

echo "\n======================================\n";
echo "API Information\n";
echo "======================================\n";
echo "Base URL: " . API_BASE . "\n";
echo "Test User Email: " . TEST_EMAIL . "\n";
echo "Test User Token: " . ($token ? substr($token, 0, 20) . '...' : 'Not obtained') . "\n";
echo "Documentation: See API_DOCUMENTATION.md\n";
?>
