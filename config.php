<?php
/**
 * Registration Portal Configuration File
 * Centralized configuration settings for the application
 */

// ====================================
// Environment Configuration
// ====================================
define('APP_ENV', 'development'); // development, staging, production
define('DEBUG_MODE', true); // Set to false in production
define('APP_VERSION', '1.0.0');

// ====================================
// Application Settings
// ====================================
$config = [
    'app' => [
        'name' => 'Registration Portal',
        'url' => 'https://your-domain.com',
        'timezone' => 'America/New_York',
        'charset' => 'UTF-8'
    ],
    
    // File Upload Configuration
    'upload' => [
        'directory' => 'uploads/',
        'max_file_size' => 10 * 1024 * 1024, // 10MB in bytes
        'allowed_types' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/jpg',
            'image/png'
        ],
        'allowed_extensions' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']
    ],
    
    // Email Configuration
    'email' => [
        'method' => 'php_mail', // php_mail, smtp
        'admin_email' => 'admin@your-domain.com',
        'from_email' => 'noreply@your-domain.com',
        'from_name' => 'Registration Portal',
        'reply_to' => 'support@your-domain.com',
        
        // SMTP Settings (if using SMTP)
        'smtp' => [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'username' => 'your-email@gmail.com',
            'password' => 'your-app-password',
            'encryption' => 'tls' // tls, ssl
        ]
    ],
    
    // Database Configuration (Optional)
    'database' => [
        'enabled' => false, // Set to true to use database instead of JSON
        'host' => 'localhost',
        'dbname' => 'registration_portal',
        'username' => 'your_db_username',
        'password' => 'your_db_password',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    ],
    
    // Data Storage
    'data' => [
        'storage_method' => 'json', // json, database
        'submissions_file' => 'data/submissions.json',
        'backup_enabled' => true,
        'backup_directory' => 'backups/',
        'retention_days' => 365
    ],
    
    // Security Settings
    'security' => [
        'csrf_protection' => true,
        'rate_limiting' => [
            'enabled' => true,
            'max_attempts' => 3,
            'time_window' => 300, // 5 minutes in seconds
        ],
        'ip_whitelist' => [], // Empty array means all IPs allowed
        'ip_blacklist' => [],
        'encryption_key' => 'your-32-character-encryption-key-here', // Generate secure key
        'session_timeout' => 1800 // 30 minutes
    ],
    
    // Logging Configuration
    'logging' => [
        'enabled' => true,
        'log_file' => 'logs/application.log',
        'log_level' => 'INFO', // DEBUG, INFO, WARNING, ERROR
        'max_file_size' => 10 * 1024 * 1024, // 10MB
        'backup_count' => 5
    ],
    
    // Notification Settings
    'notifications' => [
        'admin_notification' => true,
        'user_confirmation' => true,
        'sms_enabled' => false,
        'slack_webhook' => '', // Optional Slack integration
        'discord_webhook' => '' // Optional Discord integration
    ],
    
    // Feature Flags
    'features' => [
        'auto_save' => true,
        'print_application' => true,
        'email_copy' => true,
        'file_preview' => true,
        'progress_indicator' => true,
        'dark_mode' => false
    ],
    
    // Validation Rules
    'validation' => [
        'min_age' => 16,
        'max_name_length' => 50,
        'max_motivation_length' => 2000,
        'required_fields' => [
            'firstName', 'lastName', 'email', 'phone',
            'dateOfBirth', 'gender', 'nationality',
            'address', 'city', 'state', 'country',
            'education', 'fieldOfStudy', 'institution',
            'graduationYear', 'workExperience', 'motivation'
        ]
    ],
    
    // Third-party Integrations
    'integrations' => [
        'google_analytics' => '',
        'google_recaptcha' => [
            'enabled' => false,
            'site_key' => '',
            'secret_key' => ''
        ],
        'stripe' => [
            'enabled' => false,
            'publishable_key' => '',
            'secret_key' => ''
        ]
    ]
];

// ====================================
// Helper Functions
// ====================================
function getConfig($key = null) {
    global $config;
    
    if ($key === null) {
        return $config;
    }
    
    $keys = explode('.', $key);
    $value = $config;
    
    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return null;
        }
        $value = $value[$k];
    }
    
    return $value;
}

function setConfig($key, $value) {
    global $config;
    
    $keys = explode('.', $key);
    $current = &$config;
    
    foreach ($keys as $k) {
        if (!isset($current[$k])) {
            $current[$k] = [];
        }
        $current = &$current[$k];
    }
    
    $current = $value;
}

// ====================================
// Environment-specific Overrides
// ====================================
switch (APP_ENV) {
    case 'development':
        setConfig('security.rate_limiting.enabled', false);
        setConfig('logging.log_level', 'DEBUG');
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        break;
        
    case 'staging':
        setConfig('logging.log_level', 'INFO');
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE);
        break;
        
    case 'production':
        setConfig('security.rate_limiting.enabled', true);
        setConfig('logging.log_level', 'WARNING');
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        break;
}

// ====================================
// Load Environment Variables
// ====================================
if (file_exists('.env')) {
    $envVars = parse_ini_file('.env');
    
    // Override config with environment variables
    if (isset($envVars['DB_HOST'])) {
        setConfig('database.host', $envVars['DB_HOST']);
    }
    if (isset($envVars['DB_NAME'])) {
        setConfig('database.dbname', $envVars['DB_NAME']);
    }
    if (isset($envVars['DB_USER'])) {
        setConfig('database.username', $envVars['DB_USER']);
    }
    if (isset($envVars['DB_PASS'])) {
        setConfig('database.password', $envVars['DB_PASS']);
    }
    if (isset($envVars['ADMIN_EMAIL'])) {
        setConfig('email.admin_email', $envVars['ADMIN_EMAIL']);
    }
    if (isset($envVars['FROM_EMAIL'])) {
        setConfig('email.from_email', $envVars['FROM_EMAIL']);
    }
}

// Set timezone
if (getConfig('app.timezone')) {
    date_default_timezone_set(getConfig('app.timezone'));
}

return $config;
?>