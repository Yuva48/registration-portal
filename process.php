<?php
/**
 * Professional Registration Portal - Form Processing Backend
 * Secure PHP handler for form submission, validation, and data processing
 * Version: 1.0.0
 */

// ====================================
// Security Headers and Configuration
// ====================================
header('Content-Type: application/json');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start session for CSRF protection
session_start();

// ====================================
// Configuration Settings
// ====================================
$config = [
    'upload_dir' => 'uploads/',
    'max_file_size' => 10 * 1024 * 1024, // 10MB
    'allowed_file_types' => [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/jpg',
        'image/png'
    ],
    'data_file' => 'data/submissions.json',
    'email' => [
        'admin_email' => 'admin@registrationportal.com',
        'from_email' => 'noreply@registrationportal.com',
        'from_name' => 'Registration Portal'
    ],
    'database' => [
        // Configure if using database instead of JSON
        'host' => 'localhost',
        'dbname' => 'registration_portal',
        'username' => 'your_db_user',
        'password' => 'your_db_password'
    ]
];

// ====================================
// Main Processing Logic
// ====================================
try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Validate and sanitize input data
    $formData = validateAndSanitizeInput($_POST);
    
    // Handle file uploads
    $uploadedFiles = handleFileUploads($_FILES, $config);
    
    // Generate unique submission ID
    $submissionId = generateSubmissionId();
    
    // Prepare submission data
    $submissionData = [
        'id' => $submissionId,
        'timestamp' => date('Y-m-d H:i:s'),
        'ip_address' => getClientIpAddress(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'form_data' => $formData,
        'uploaded_files' => $uploadedFiles,
        'status' => 'pending'
    ];
    
    // Save submission data
    saveSubmissionData($submissionData, $config);
    
    // Send confirmation emails
    sendConfirmationEmails($submissionData, $config);
    
    // Log successful submission
    logActivity("Submission successful: {$submissionId}", 'INFO');
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully',
        'submissionId' => $submissionId,
        'timestamp' => $submissionData['timestamp']
    ]);
    
} catch (Exception $e) {
    // Log error
    logActivity("Submission error: " . $e->getMessage(), 'ERROR');
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// ====================================
// Input Validation and Sanitization
// ====================================
function validateAndSanitizeInput($postData) {
    $requiredFields = [
        'firstName', 'lastName', 'dateOfBirth', 'gender', 'nationality', 'idNumber',
        'email', 'phone', 'address', 'city', 'state', 'zipCode', 'country',
        'education', 'fieldOfStudy', 'institution', 'graduationYear', 'workExperience',
        'motivation', 'termsAgreement', 'privacyAgreement', 'dataProcessing'
    ];
    
    $sanitizedData = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($postData[$field]) || empty(trim($postData[$field]))) {
            if (!in_array($field, ['termsAgreement', 'privacyAgreement', 'dataProcessing'])) {
                throw new Exception("Required field '{$field}' is missing or empty");
            }
        }
        
        // Sanitize based on field type
        $value = trim($postData[$field] ?? '');
        
        switch ($field) {
            case 'email':
                $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Invalid email address format');
                }
                break;
                
            case 'phone':
            case 'alternatePhone':
                $value = preg_replace('/[^+\d\s\-\(\)]/', '', $value);
                if (!empty($value) && !preg_match('/^[\+]?[\d\s\-\(\)]{10,}$/', $value)) {
                    throw new Exception('Invalid phone number format');
                }
                break;
                
            case 'dateOfBirth':
                if (!DateTime::createFromFormat('Y-m-d', $value)) {
                    throw new Exception('Invalid date of birth format');
                }
                // Validate age (minimum 16)
                $birthDate = new DateTime($value);
                $today = new DateTime();
                $age = $today->diff($birthDate)->y;
                if ($age < 16) {
                    throw new Exception('Applicant must be at least 16 years old');
                }
                break;
                
            case 'firstName':
            case 'lastName':
                $value = trim(strip_tags($value));
                if (!preg_match('/^[a-zA-Z\s\-\.\']/', $value)) {
                    throw new Exception("Invalid {$field} format");
                }
                break;
                
            case 'graduationYear':
                $value = (int)$value;
                $currentYear = date('Y');
                if ($value < ($currentYear - 50) || $value > ($currentYear + 5)) {
                    throw new Exception('Invalid graduation year');
                }
                break;
                
            case 'termsAgreement':
            case 'privacyAgreement':
            case 'dataProcessing':
                if ($value !== 'on' && $value !== '1' && $value !== 'true') {
                    throw new Exception("Required agreement '{$field}' is not checked");
                }
                $value = true;
                break;
                
            case 'newsletter':
                $value = ($value === 'on' || $value === '1' || $value === 'true');
                break;
                
            default:
                $value = trim(strip_tags($value));
                break;
        }
        
        $sanitizedData[$field] = $value;
    }
    
    // Optional fields
    $optionalFields = ['alternatePhone', 'currentPosition', 'skills', 'newsletter'];
    foreach ($optionalFields as $field) {
        if (isset($postData[$field])) {
            $value = trim($postData[$field]);
            if ($field === 'newsletter') {
                $value = ($value === 'on' || $value === '1' || $value === 'true');
            } else {
                $value = trim(strip_tags($value));
            }
            $sanitizedData[$field] = $value;
        }
    }
    
    return $sanitizedData;
}

// ====================================
// File Upload Handling
// ====================================
function handleFileUploads($files, $config) {
    $uploadedFiles = [];
    
    if (!isset($files['documents']) || empty($files['documents']['name'][0])) {
        return $uploadedFiles;
    }
    
    // Create upload directory if it doesn't exist
    if (!file_exists($config['upload_dir'])) {
        if (!mkdir($config['upload_dir'], 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    $documents = $files['documents'];
    
    // Handle multiple files
    for ($i = 0; $i < count($documents['name']); $i++) {
        if ($documents['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        
        $originalName = $documents['name'][$i];
        $fileSize = $documents['size'][$i];
        $fileTmpPath = $documents['tmp_name'][$i];
        $fileType = $documents['type'][$i];
        
        // Validate file size
        if ($fileSize > $config['max_file_size']) {
            throw new Exception("File '{$originalName}' exceeds maximum size limit");
        }
        
        // Validate file type - simplified approach
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("File '{$originalName}' has an unsupported format. Allowed: PDF, DOC, DOCX, JPG, PNG");
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeFileName = generateSafeFileName($originalName, $fileExtension);
        $uploadPath = $config['upload_dir'] . $safeFileName;
        
        // Move uploaded file
        if (!move_uploaded_file($fileTmpPath, $uploadPath)) {
            throw new Exception("Failed to save file '{$originalName}'");
        }
        
        $uploadedFiles[] = [
            'original_name' => $originalName,
            'safe_name' => $safeFileName,
            'file_path' => $uploadPath,
            'file_size' => $fileSize,
            'file_type' => $fileExtension,
            'upload_time' => date('Y-m-d H:i:s')
        ];
    }
    
    return $uploadedFiles;
}

// ====================================
// Data Storage Functions
// ====================================
function saveSubmissionData($submissionData, $config) {
    // Create data directory if it doesn't exist
    $dataDir = dirname($config['data_file']);
    if (!file_exists($dataDir)) {
        if (!mkdir($dataDir, 0755, true)) {
            throw new Exception('Failed to create data directory');
        }
    }
    
    // Load existing submissions
    $submissions = [];
    if (file_exists($config['data_file'])) {
        $jsonData = file_get_contents($config['data_file']);
        $submissions = json_decode($jsonData, true) ?? [];
    }
    
    // Add new submission
    $submissions[] = $submissionData;
    
    // Save updated data
    $jsonData = json_encode($submissions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($config['data_file'], $jsonData, LOCK_EX) === false) {
        throw new Exception('Failed to save submission data');
    }
    
    // Also save individual submission file
    $individualFile = "data/submission_{$submissionData['id']}.json";
    file_put_contents($individualFile, json_encode($submissionData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// ====================================
// Email Functions
// ====================================
function sendConfirmationEmails($submissionData, $config) {
    // Send confirmation to applicant
    sendApplicantConfirmation($submissionData, $config);
    
    // Send notification to admin
    sendAdminNotification($submissionData, $config);
}

function sendApplicantConfirmation($submissionData, $config) {
    $to = $submissionData['form_data']['email'];
    $subject = 'Registration Application Confirmation';
    
    $message = generateConfirmationEmail($submissionData);
    
    $headers = [
        'From: ' . $config['email']['from_name'] . ' <' . $config['email']['from_email'] . '>',
        'Reply-To: ' . $config['email']['admin_email'],
        'Content-Type: text/html; charset=UTF-8',
        'MIME-Version: 1.0'
    ];
    
    mail($to, $subject, $message, implode("\r\n", $headers));
}

function sendAdminNotification($submissionData, $config) {
    $to = $config['email']['admin_email'];
    $subject = 'New Registration Application Received';
    
    $message = generateAdminNotificationEmail($submissionData);
    
    $headers = [
        'From: ' . $config['email']['from_name'] . ' <' . $config['email']['from_email'] . '>',
        'Content-Type: text/html; charset=UTF-8',
        'MIME-Version: 1.0'
    ];
    
    mail($to, $subject, $message, implode("\r\n", $headers));
}

function generateConfirmationEmail($submissionData) {
    $formData = $submissionData['form_data'];
    
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .info-box { background: #f8fafc; padding: 15px; border-left: 4px solid #2563eb; margin: 10px 0; }
            .footer { background: #f1f5f9; padding: 15px; text-align: center; font-size: 0.9em; color: #64748b; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>Registration Application Confirmed</h2>
        </div>
        <div class='content'>
            <p>Dear {$formData['firstName']} {$formData['lastName']},</p>
            
            <p>Thank you for submitting your registration application. We have successfully received your information and it is currently being processed.</p>
            
            <div class='info-box'>
                <strong>Application Details:</strong><br>
                <strong>Submission ID:</strong> {$submissionData['id']}<br>
                <strong>Date & Time:</strong> {$submissionData['timestamp']}<br>
                <strong>Email:</strong> {$formData['email']}<br>
                <strong>Phone:</strong> {$formData['phone']}
            </div>
            
            <p><strong>Next Steps:</strong></p>
            <ul>
                <li>Your application will be reviewed within 3-5 business days</li>
                <li>You will receive an email notification about the status</li>
                <li>Please keep this confirmation email for your records</li>
                <li>If you have any questions, please contact us using your Submission ID</li>
            </ul>
            
            <p><strong>Important:</strong> Please do not reply to this automated email. If you need assistance, please contact our support team.</p>
        </div>
        <div class='footer'>
            <p>&copy; " . date('Y') . " Registration Portal. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </body>
    </html>
    ";
}

function generateAdminNotificationEmail($submissionData) {
    $formData = $submissionData['form_data'];
    
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: #059669; color: white; padding: 20px; }
            .content { padding: 20px; }
            .detail-box { background: #f0f9ff; padding: 15px; margin: 10px 0; border-radius: 5px; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background-color: #f8fafc; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>New Registration Application</h2>
        </div>
        <div class='content'>
            <div class='detail-box'>
                <strong>Submission ID:</strong> {$submissionData['id']}<br>
                <strong>Date & Time:</strong> {$submissionData['timestamp']}<br>
                <strong>IP Address:</strong> {$submissionData['ip_address']}
            </div>
            
            <h3>Applicant Information</h3>
            <table>
                <tr><th>Field</th><th>Value</th></tr>
                <tr><td>Name</td><td>{$formData['firstName']} {$formData['lastName']}</td></tr>
                <tr><td>Email</td><td>{$formData['email']}</td></tr>
                <tr><td>Phone</td><td>{$formData['phone']}</td></tr>
                <tr><td>Date of Birth</td><td>{$formData['dateOfBirth']}</td></tr>
                <tr><td>Gender</td><td>{$formData['gender']}</td></tr>
                <tr><td>Nationality</td><td>{$formData['nationality']}</td></tr>
                <tr><td>Address</td><td>{$formData['address']}, {$formData['city']}, {$formData['state']} {$formData['zipCode']}, {$formData['country']}</td></tr>
                <tr><td>Education</td><td>{$formData['education']}</td></tr>
                <tr><td>Field of Study</td><td>{$formData['fieldOfStudy']}</td></tr>
                <tr><td>Institution</td><td>{$formData['institution']}</td></tr>
                <tr><td>Work Experience</td><td>{$formData['workExperience']}</td></tr>
            </table>
            
            <h3>Files Uploaded</h3>
            " . (count($submissionData['uploaded_files']) > 0 ? 
                "<ul>" . implode('', array_map(function($file) {
                    return "<li>{$file['original_name']} (" . formatFileSize($file['file_size']) . ")</li>";
                }, $submissionData['uploaded_files'])) . "</ul>" 
                : "<p>No files uploaded</p>") . "
            
            <p><strong>Review this application in the admin panel.</strong></p>
        </div>
    </body>
    </html>
    ";
}

// ====================================
// Utility Functions
// ====================================
function generateSubmissionId() {
    return 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
}

function generateSafeFileName($originalName, $extension) {
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $safeName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $baseName);
    $safeName = substr($safeName, 0, 50); // Limit length
    return $safeName . '_' . time() . '.' . $extension;
}

function getClientIpAddress() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round(($bytes / pow($k, $i)), 2) . ' ' . $sizes[$i];
}

function logActivity($message, $level = 'INFO') {
    $logFile = 'logs/activity.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = getClientIpAddress();
    $logEntry = "[{$timestamp}] [{$level}] [IP: {$ip}] {$message}" . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// ====================================
// Security Functions
// ====================================
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    return $input;
}

function rateLimitCheck() {
    // Simple rate limiting based on IP
    $ip = getClientIpAddress();
    $rateLimitFile = "tmp/rate_limit_{$ip}.txt";
    
    if (file_exists($rateLimitFile)) {
        $lastSubmission = (int)file_get_contents($rateLimitFile);
        if (time() - $lastSubmission < 60) { // 1 minute cooldown
            throw new Exception('Too many submission attempts. Please wait before trying again.');
        }
    }
    
    // Create tmp directory if it doesn't exist
    if (!file_exists('tmp')) {
        mkdir('tmp', 0755, true);
    }
    
    file_put_contents($rateLimitFile, time());
}

// Apply rate limiting
// rateLimitCheck(); // Uncomment to enable rate limiting

?>