<?php
/**
 * API Configuration File (EXAMPLE)
 * 
 * Copy this file to a secure location outside the web root
 * and update with your actual configuration.
 * 
 * For production:
 * - Use environment variables
 * - Never commit sensitive data to version control
 * - Use secrets manager (AWS Secrets Manager, HashiCorp Vault, etc)
 */

// Environment detection
define('ENVIRONMENT', getenv('APP_ENV') ?? 'development');
define('DEBUG', ENVIRONMENT === 'development');

// API Configuration
define('API_NAME', 'GroceryPlus');
define('API_VERSION', '1.0');
define('API_BASE_URL', getenv('API_BASE_URL') ?? 'http://localhost/groceryplus/api');
define('APP_BASE_URL', getenv('APP_BASE_URL') ?? 'http://localhost/groceryplus');

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?? 'localhost');
define('DB_PORT', getenv('DB_PORT') ?? 3306);
define('DB_NAME', getenv('DB_NAME') ?? 'groceryplus');
define('DB_USER', getenv('DB_USER') ?? 'root');
define('DB_PASS', getenv('DB_PASS') ?? '');
define('DB_TYPE', getenv('DB_TYPE') ?? 'sqlite'); // mysql, sqlite, postgresql

// SQLite specific
define('SQLITE_PATH', getenv('SQLITE_PATH') ?? __DIR__ . '/../../GroceryPlusDB.db');

// Authentication
define('AUTH_SECRET_KEY', getenv('AUTH_SECRET_KEY') ?? 'change_this_secret_key_in_production');
define('AUTH_TOKEN_ALGORITHM', 'HS256');
define('AUTH_TOKEN_EXPIRY', 86400 * 7); // 7 days in seconds
define('REFRESH_TOKEN_EXPIRY', 86400 * 30); // 30 days

// Security
define('PASSWORD_HASH_ALGORITHM', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 12);
define('CSRF_TOKEN_LENGTH', 32);

// File Upload
define('UPLOAD_DIR', getenv('UPLOAD_DIR') ?? __DIR__ . '/../../images/');
define('MAX_FILE_SIZE', getenv('MAX_FILE_SIZE') ?? 5 * 1024 * 1024); // 5MB
define('MAX_IMAGE_WIDTH', 2000);
define('MAX_IMAGE_HEIGHT', 2000);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Rate Limiting (future feature)
define('RATE_LIMIT_ENABLED', false);
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_PERIOD', 60); // seconds

// CORS Configuration
define('CORS_ALLOWED_ORIGINS', [
    'http://localhost:3000',
    'http://localhost:8000',
    'http://192.168.1.100:3000',
    // Add your mobile app URLs here
]);
define('CORS_ALLOW_CREDENTIALS', true);
define('CORS_MAX_AGE', 86400);

// Email Configuration (for notifications)
define('MAIL_DRIVER', getenv('MAIL_DRIVER') ?? 'smtp');
define('MAIL_HOST', getenv('MAIL_HOST') ?? 'smtp.mailtrap.io');
define('MAIL_PORT', getenv('MAIL_PORT') ?? 587);
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?? '');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?? '');
define('MAIL_FROM_ADDRESS', getenv('MAIL_FROM_ADDRESS') ?? 'noreply@groceryplus.com');
define('MAIL_FROM_NAME', 'GroceryPlus');

// Logging
define('LOG_LEVEL', getenv('LOG_LEVEL') ?? 'error'); // error, warning, info, debug
define('LOG_DIR', getenv('LOG_DIR') ?? __DIR__ . '/../../logs/');
define('LOG_MAX_SIZE', 10 * 1024 * 1024); // 10MB per file

// Cache Configuration (future feature)
define('CACHE_DRIVER', getenv('CACHE_DRIVER') ?? 'file');
define('CACHE_TTL', 3600); // 1 hour

// Analytics
define('ANALYTICS_ENABLED', getenv('ANALYTICS_ENABLED') ?? false);
define('ANALYTICS_TRACK_PERFORMANCE', DEBUG === false);

// Pagination
define('DEFAULT_LIMIT', 50);
define('MAX_LIMIT', 100);
define('DEFAULT_OFFSET', 0);

// API Response
define('INCLUDE_TIMESTAMP', true);
define('INCLUDE_REQUEST_ID', true);
define('JSON_PRETTY_PRINT', DEBUG);

// Admin Configuration
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?? 'admin@groceryplus.com');
define('ADMIN_DEFAULT_PASSWORD', getenv('ADMIN_DEFAULT_PASSWORD') ?? 'admin123'); // CHANGE IN PRODUCTION

// Feature Flags
define('FEATURE_FILE_UPLOAD', true);
define('FEATURE_REVIEWS', true);
define('FEATURE_FAVORITES', true);
define('FEATURE_MESSAGING', true);
define('FEATURE_NOTIFICATIONS', true);
define('FEATURE_ANALYTICS', true);

// Third-party Services
define('AWS_S3_ENABLED', false);
define('AWS_S3_KEY', getenv('AWS_S3_KEY') ?? '');
define('AWS_S3_SECRET', getenv('AWS_S3_SECRET') ?? '');
define('AWS_S3_REGION', getenv('AWS_S3_REGION') ?? 'us-east-1');
define('AWS_S3_BUCKET', getenv('AWS_S3_BUCKET') ?? '');

// Mobile App Configuration
define('ANDROID_APP_NAME', 'GroceryPlus');
define('ANDROID_PACKAGE_NAME', 'com.groceryplus.app');
define('IOS_BUNDLE_ID', 'com.groceryplus.app');
define('CURRENT_APP_VERSION', '1.0.0');
define('MIN_SUPPORTED_VERSION', '1.0.0');

// Time Zone
define('APP_TIMEZONE', getenv('APP_TIMEZONE') ?? 'UTC');
date_default_timezone_set(APP_TIMEZONE);

// Pagination Constants
define('PAGINATION_PAGE_SIZE_MIN', 1);
define('PAGINATION_PAGE_SIZE_MAX', 100);
define('PAGINATION_PAGE_SIZE_DEFAULT', 50);

/**
 * Configuration Validation
 */
function validateConfiguration() {
    $errors = [];
    
    // Check database configuration
    if (DB_TYPE === 'sqlite' && !file_exists(SQLITE_PATH)) {
        $errors[] = "SQLite database file not found: " . SQLITE_PATH;
    }
    
    // Check upload directory
    if (!is_dir(UPLOAD_DIR)) {
        if (!@mkdir(UPLOAD_DIR, 0755, true)) {
            $errors[] = "Upload directory cannot be created: " . UPLOAD_DIR;
        }
    }
    
    if (!is_writable(UPLOAD_DIR)) {
        $errors[] = "Upload directory is not writable: " . UPLOAD_DIR;
    }
    
    // Check log directory
    if (!is_dir(LOG_DIR)) {
        if (!@mkdir(LOG_DIR, 0755, true)) {
            $errors[] = "Log directory cannot be created: " . LOG_DIR;
        }
    }
    
    // Check for development defaults in production
    if (ENVIRONMENT === 'production') {
        if (AUTH_SECRET_KEY === 'change_this_secret_key_in_production') {
            $errors[] = "AUTH_SECRET_KEY must be changed from default in production";
        }
        
        if (ADMIN_DEFAULT_PASSWORD === 'admin123') {
            $errors[] = "Admin default password must be changed in production";
        }
        
        if (DEBUG === true) {
            $errors[] = "DEBUG must be false in production";
        }
    }
    
    return $errors;
}

// Validate on load
$configErrors = validateConfiguration();
if (!empty($configErrors)) {
    error_log("Configuration errors: " . json_encode($configErrors));
    if (DEBUG) {
        echo json_encode(['errors' => $configErrors]);
        die;
    }
}

/**
 * Helper Functions
 */

function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

function isProduction() {
    return ENVIRONMENT === 'production';
}

function isDevelopment() {
    return ENVIRONMENT === 'development';
}

function getApiUrl($endpoint = '') {
    return API_BASE_URL . ($endpoint ? '/' . ltrim($endpoint, '/') : '');
}

function getAppUrl($path = '') {
    return APP_BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Environment-specific settings
 */
if (isDevelopment()) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

?>
