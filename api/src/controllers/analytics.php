<?php

class AnalyticsController {
    public static function handleAnalytics($method, $pdo) {
        if ($method !== 'GET') {
            sendError('Method not allowed', 405);
        }

        if (!validateToken()) {
            sendError('Unauthorized', 401);
        }

        $user = getUserFromToken();
        if ($user['type'] !== 'admin') {
            sendError('Admin access required', 403);
        }

        // Get various analytics
        $analytics = [];

        // Revenue analytics
        $stmt = $pdo->query("
            SELECT
                SUM(CASE WHEN DATE(created_at) = DATE('now') THEN total_amount ELSE 0 END) as today_revenue,
                SUM(CASE WHEN created_at >= datetime('now', '-7 days') THEN total_amount ELSE 0 END) as week_revenue,
                AVG(total_amount) as avg_order_value
            FROM orders
            WHERE status = 'delivered'
        ");
        $analytics['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Order analytics
        $stmt = $pdo->query("
            SELECT
                COUNT(CASE WHEN DATE(created_at) = DATE('now') THEN 1 END) as today_orders,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as completed_orders
            FROM orders
        ");
        $analytics['orders'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // User analytics
        $stmt = $pdo->query("
            SELECT
                COUNT(*) as total_users,
                COUNT(CASE WHEN DATE(created_at) >= datetime('now', '-30 days') THEN 1 END) as new_users
            FROM users
        ");
        $analytics['users'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Product analytics
        $stmt = $pdo->query("
            SELECT
                COUNT(*) as total_products,
                COUNT(CASE WHEN stock_quantity <= 10 THEN 1 END) as low_stock
            FROM products
        ");
        $analytics['products'] = $stmt->fetch(PDO::FETCH_ASSOC);

        sendResponse($analytics);
    }
}
?>