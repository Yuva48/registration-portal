<?php
/**
 * Simplified Registration Portal - Form Processing Backend
 * Basic version for testing form submission
 */

header('Content-Type: application/json');

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Get form data
    $formData = $_POST;
    
    // Basic validation
    $required = ['firstName', 'lastName', 'email', 'phone'];
    foreach ($required as $field) {
        if (empty($formData[$field])) {
            throw new Exception("Required field '$field' is missing");
        }
    }
    
    // Validate email
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    // Generate submission ID
    $submissionId = 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    
    // Prepare submission data
    $submissionData = [
        'id' => $submissionId,
        'timestamp' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'form_data' => $formData,
        'status' => 'submitted'
    ];
    
    // Create data directory if it doesn't exist
    if (!file_exists('data')) {
        mkdir('data', 0755, true);
    }
    
    // Save to individual file
    $fileName = "data/submission_{$submissionId}.json";
    file_put_contents($fileName, json_encode($submissionData, JSON_PRETTY_PRINT));
    
    // Also append to main submissions file
    $submissions = [];
    $mainFile = 'data/submissions.json';
    if (file_exists($mainFile)) {
        $jsonData = file_get_contents($mainFile);
        $submissions = json_decode($jsonData, true) ?? [];
    }
    $submissions[] = $submissionData;
    file_put_contents($mainFile, json_encode($submissions, JSON_PRETTY_PRINT));
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully!',
        'submissionId' => $submissionId,
        'timestamp' => $submissionData['timestamp']
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>