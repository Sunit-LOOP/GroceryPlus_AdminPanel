<?php
// GroceryPlus API Documentation and Test Interface
// This provides both web interface and API documentation

$baseUrl = "http://localhost/groceryplus/api/";

// Handle API calls
if (isset($_GET['test'])) {
    testApiEndpoints();
    exit;
}

function testApiEndpoints() {
    global $baseUrl;

    echo "<h2>API Test Results</h2>";

    // Test 1: Products
    echo "<h3>1. Products API</h3>";
    $result = file_get_contents($baseUrl . "index.php/products");
    if ($result) {
        $data = json_decode($result, true);
        echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo "<p style='color:red'>❌ Failed to fetch products</p>";
    }

    // Test 2: Categories
    echo "<h3>2. Categories API</h3>";
    $result = file_get_contents($baseUrl . "index.php/categories");
    if ($result) {
        $data = json_decode($result, true);
        echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo "<p style='color:red'>❌ Failed to fetch categories</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroceryPlus API Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .api-section { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .endpoint { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #007bff; }
        .method { font-weight: bold; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8em; }
        .method.get { background: #28a745; }
        .method.post { background: #007bff; }
        .method.put { background: #ffc107; color: black; }
        .method.delete { background: #dc3545; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1><i class="fas fa-plug text-primary"></i> GroceryPlus API</h1>
            <p class="lead">Complete REST API for Android App ↔ Web Application Synchronization</p>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>API Status:</strong> <span class="status-badge status-success">ONLINE</span>
                - Ready for Android and Web integration
            </div>
        </div>

        <!-- Quick Test -->
        <div class="api-section">
            <h2><i class="fas fa-flask"></i> Quick API Test</h2>
            <p>Test the API endpoints to ensure they're working:</p>
            <a href="?test=1" class="btn btn-primary">
                <i class="fas fa-play"></i> Run API Tests
            </a>
        </div>

        <!-- Authentication -->
        <div class="api-section">
            <h2><i class="fas fa-key"></i> Authentication</h2>
            <div class="endpoint">
                <h4><span class="method post">POST</span> /api/index.php/auth</h4>
                <p>Authenticate user and get access token</p>
                <h6>Request Body:</h6>
                <pre>{
  "email": "user@example.com",
  "password": "password123"
}</pre>
                <h6>Response:</h6>
                <pre>{
  "token": "user_123_1640995200_abc123",
  "user": {
    "id": 123,
    "name": "John Doe",
    "email": "user@example.com",
    "type": "user"
  }
}</pre>
            </div>
        </div>

        <!-- User Registration -->
        <div class="api-section">
            <h2><i class="fas fa-user-plus"></i> User Registration</h2>
            <div class="endpoint">
                <h4><span class="method post">POST</span> /api/index.php/register</h4>
                <p>Register a new user account</p>
                <h6>Request Body:</h6>
                <pre>{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "securepassword",
  "phone": "+1234567890"
}</pre>
            </div>
        </div>

        <!-- Products API -->
        <div class="api-section">
            <h2><i class="fas fa-box"></i> Products API</h2>

            <div class="endpoint">
                <h4><span class="method get">GET</span> /api/index.php/products</h4>
                <p>Get all products (with optional filtering)</p>
                <h6>Query Parameters:</h6>
                <ul>
                    <li><code>category=fruits</code> - Filter by category</li>
                    <li><code>search=apple</code> - Search in product names</li>
                    <li><code>limit=10&offset=0</code> - Pagination</li>
                </ul>
            </div>

            <div class="endpoint">
                <h4><span class="method get">GET</span> /api/index.php/products/{id}</h4>
                <p>Get specific product details</p>
            </div>

            <div class="endpoint">
                <h4><span class="method post">POST</span> /api/index.php/products</h4>
                <p>Create new product (Admin only)</p>
                <h6>Headers:</h6>
                <pre>Authorization: admin_token_here</pre>
            </div>

            <div class="endpoint">
                <h4><span class="method put">PUT</span> /api/index.php/products/{id}</h4>
                <p>Update product (Admin only)</p>
            </div>
        </div>

        <!-- Categories API -->
        <div class="api-section">
            <h2><i class="fas fa-folder"></i> Categories API</h2>

            <div class="endpoint">
                <h4><span class="method get">GET</span> /api/index.php/categories</h4>
                <p>Get all product categories</p>
            </div>

            <div class="endpoint">
                <h4><span class="method post">POST</span> /api/index.php/categories</h4>
                <p>Create new category (Admin only)</p>
            </div>
        </div>

        <!-- Orders API -->
        <div class="api-section">
            <h2><i class="fas fa-shopping-cart"></i> Orders API</h2>

            <div class="endpoint">
                <h4><span class="method get">GET</span> /api/index.php/orders</h4>
                <p>Get user's order history</p>
            </div>

            <div class="endpoint">
                <h4><span class="method post">POST</span> /api/index.php/orders</h4>
                <p>Place new order</p>
                <h6>Request Body:</h6>
                <pre>{
  "user_id": 123,
  "items": [
    {"product_id": 1, "quantity": 2, "price": 5.99},
    {"product_id": 2, "quantity": 1, "price": 3.49}
  ],
  "delivery_fee": 2.99
}</pre>
            </div>

            <div class="endpoint">
                <h4><span class="method put">PUT</span> /api/index.php/orders/{id}</h4>
                <p>Update order status (Admin) or cancel order (User)</p>
            </div>
        </div>

        <!-- Cart API -->
        <div class="api-section">
            <h2><i class="fas fa-cart-plus"></i> Shopping Cart API</h2>

            <div class="endpoint">
                <h4><span class="method get">GET</span> /api/index.php/cart</h4>
                <p>Get user's cart items</p>
            </div>

            <div class="endpoint">
                <h4><span class="method post">POST</span> /api/index.php/cart</h4>
                <p>Add item to cart</p>
                <h6>Request Body:</h6>
                <pre>{
  "product_id": 1,
  "quantity": 2
}</pre>
            </div>

            <div class="endpoint">
                <h4><span class="method put">PUT</span> /api/index.php/cart/{item_id}</h4>
                <p>Update cart item quantity</p>
            </div>

            <div class="endpoint">
                <h4><span class="method delete">DELETE</span> /api/index.php/cart/{item_id}</h4>
                <p>Remove item from cart</p>
            </div>
        </div>

        <!-- Messages API -->
        <div class="api-section">
            <h2><i class="fas fa-comments"></i> Messages API</h2>

            <div class="endpoint">
                <h4><span class="method get">GET</span> /api/index.php/messages</h4>
                <p>Get conversation list</p>
            </div>

            <div class="endpoint">
                <h4><span class="method get">GET</span> /api/index.php/messages/{user_id}</h4>
                <p>Get messages with specific user</p>
            </div>

            <div class="endpoint">
                <h4><span class="method post">POST</span> /api/index.php/messages</h4>
                <p>Send new message</p>
                <h6>Request Body:</h6>
                <pre>{
  "receiver_id": 456,
  "message": "Hello from API!"
}</pre>
            </div>
        </div>

        <!-- Reviews API -->
        <div class="api-section">
            <h2><i class="fas fa-star"></i> Reviews API</h2>

            <div class="endpoint">
                <h4><span class="method get">GET</span> /api/index.php/reviews/{product_id}</h4>
                <p>Get reviews for specific product</p>
            </div>

            <div class="endpoint">
                <h4><span class="method post">POST</span> /api/index.php/reviews</h4>
                <p>Submit product review</p>
                <h6>Request Body:</h6>
                <pre>{
  "product_id": 1,
  "rating": 5,
  "review": "Excellent product!"
}</pre>
            </div>
        </div>

        <!-- Analytics API -->
        <div class="api-section">
            <h2><i class="fas fa-chart-bar"></i> Analytics API (Admin Only)</h2>

            <div class="endpoint">
                <h4><span class="method get">GET</span> /api/index.php/analytics</h4>
                <p>Get business analytics and KPIs</p>
                <h6>Response includes:</h6>
                <ul>
                    <li>Revenue metrics (today, week, total)</li>
                    <li>Order statistics and trends</li>
                    <li>User analytics and growth</li>
                    <li>Product performance data</li>
                </ul>
            </div>
        </div>

        <!-- Android Integration Guide -->
        <div class="api-section">
            <h2><i class="fab fa-android"></i> Android App Integration</h2>

            <h4>Base URL:</h4>
            <pre>http://your-server-ip/groceryplus/api/index.php/</pre>

            <h4>Sample Android Code:</h4>
            <pre>// Authentication
JSONObject authData = new JSONObject();
authData.put("email", "user@example.com");
authData.put("password", "password123");

JsonObjectRequest authRequest = new JsonObjectRequest(Request.Method.POST,
    "http://your-server-ip/groceryplus/api/index.php/auth", authData,
    response -> {
        String token = response.getString("token");
        // Store token for future requests
    },
    error -> Log.e("API", "Auth failed"));

// Add token to subsequent requests
Map&lt;String, String&gt; headers = new HashMap&lt;&gt;();
headers.put("Authorization", token);

JsonObjectRequest productsRequest = new JsonObjectRequest(Request.Method.GET,
    "http://your-server-ip/groceryplus/api/index.php/products", null,
    response -> {
        // Handle products response
    },
    error -> Log.e("API", "Request failed")) {
    @Override
    public Map&lt;String, String&gt; getHeaders() {
        return headers;
    }
};</pre>

            <div class="alert alert-info">
                <h5><i class="fas fa-lightbulb"></i> Pro Tips for Android Integration:</h5>
                <ul>
                    <li>Use <strong>Volley</strong> or <strong>Retrofit</strong> for HTTP requests</li>
                    <li>Store auth tokens securely using <strong>SharedPreferences</strong></li>
                    <li>Implement offline caching for better UX</li>
                    <li>Use background threads for API calls</li>
                    <li>Handle network errors gracefully</li>
                </ul>
            </div>
        </div>

        <!-- Error Codes -->
        <div class="api-section">
            <h2><i class="fas fa-exclamation-triangle"></i> Error Handling</h2>

            <h4>Common HTTP Status Codes:</h4>
            <ul>
                <li><strong>200 OK</strong> - Success</li>
                <li><strong>201 Created</strong> - Resource created</li>
                <li><strong>400 Bad Request</strong> - Invalid input</li>
                <li><strong>401 Unauthorized</strong> - Authentication required</li>
                <li><strong>403 Forbidden</strong> - Insufficient permissions</li>
                <li><strong>404 Not Found</strong> - Endpoint not found</li>
                <li><strong>500 Internal Error</strong> - Server error</li>
            </ul>

            <h4>Error Response Format:</h4>
            <pre>{
  "error": true,
  "message": "Detailed error description"
}</pre>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>