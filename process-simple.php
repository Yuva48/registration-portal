<?php
/**
 * Simple Registration Portal - Form Processing Backend
 */

// Create data directory if it doesn't exist
if (!is_dir('data')) {
    mkdir('data', 0755, true);
}

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die('Invalid request method');
    }
    
    // Get form data
    $formData = $_POST;
    
    // Basic validation
    $required = ['firstName', 'lastName', 'email', 'phone'];
    foreach ($required as $field) {
        if (empty($formData[$field])) {
            die("Error: Required field '$field' is missing");
        }
    }
    
    // Validate email
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        die('Error: Invalid email address');
    }
    
    // Generate submission ID
    $submissionId = 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    
    // Prepare submission data
    $submissionData = [
        'id' => $submissionId,
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => $formData
    ];
    
    // Save to individual file
    $fileName = "data/submission_{$submissionId}.json";
    file_put_contents($fileName, json_encode($submissionData, JSON_PRETTY_PRINT));
    
    // Save to main submissions file
    $submissions = [];
    $mainFile = 'data/submissions.json';
    if (file_exists($mainFile)) {
        $jsonData = file_get_contents($mainFile);
        $submissions = json_decode($jsonData, true) ?? [];
    }
    $submissions[] = $submissionData;
    file_put_contents($mainFile, json_encode($submissions, JSON_PRETTY_PRINT));
    
    // Redirect to success page
    header("Location: success.php?id=" . $submissionId);
    exit;
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>