<?php
/**
 * Professional Registration Portal - Success Page
 * Display submitted application information with professional formatting
 * Version: 1.0.0
 */

// Security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Get submission ID from URL parameter
$submissionId = $_GET['id'] ?? null;

if (!$submissionId) {
    header('Location: index.html');
    exit;
}

// Load submission data
$submissionData = loadSubmissionData($submissionId);

if (!$submissionData) {
    header('Location: index.html');
    exit;
}

function loadSubmissionData($submissionId) {
    $individualFile = "data/submission_{$submissionId}.json";
    
    if (file_exists($individualFile)) {
        $jsonData = file_get_contents($individualFile);
        return json_decode($jsonData, true);
    }
    
    // Fallback: search in main submissions file
    $mainFile = 'data/submissions.json';
    if (file_exists($mainFile)) {
        $jsonData = file_get_contents($mainFile);
        $submissions = json_decode($jsonData, true) ?? [];
        
        foreach ($submissions as $submission) {
            if ($submission['id'] === $submissionId) {
                return $submission;
            }
        }
    }
    
    return null;
}
}

function formatFieldValue($key, $value) {
    switch ($key) {
        case 'dateOfBirth':
            return date('F j, Y', strtotime($value));
        case 'gender':
            return ucfirst($value);
        case 'education':
            $educationLabels = [
                'high-school' => 'High School Diploma',
                'associate' => 'Associate Degree',
                'bachelor' => 'Bachelors Degree',
                'master' => 'Masters Degree',
                'doctorate' => 'Doctorate/PhD',
                'other' => 'Other'
            ];
            return $educationLabels[$value] ?? ucfirst($value);
        case 'workExperience':
            return $value . ' years';
        case 'termsAgreement':
        case 'privacyAgreement':
        case 'dataProcessing':
        case 'newsletter':
            return $value ? 'Yes' : 'No';
        case 'motivation':
            return nl2br(htmlspecialchars($value));
        case 'skills':
            if (!empty($value)) {
                $skills = explode(',', $value);
                return implode(' • ', array_map('trim', $skills));
            }
            return $value;
        default:
            return htmlspecialchars($value);
    }
}

function getFieldLabel($key) {
    $labels = [
        'firstName' => 'First Name',
        'lastName' => 'Last Name',
        'dateOfBirth' => 'Date of Birth',
        'gender' => 'Gender',
        'nationality' => 'Nationality',
        'idNumber' => 'ID/Passport Number',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'alternatePhone' => 'Alternate Phone',
        'address' => 'Street Address',
        'city' => 'City',
        'state' => 'State/Province',
        'zipCode' => 'ZIP/Postal Code',
        'country' => 'Country',
        'education' => 'Education Level',
        'fieldOfStudy' => 'Field of Study',
        'institution' => 'Institution',
        'graduationYear' => 'Graduation Year',
        'workExperience' => 'Work Experience',
        'currentPosition' => 'Current Position',
        'skills' => 'Skills & Competencies',
        'motivation' => 'Motivation for Application',
        'termsAgreement' => 'Terms & Conditions Agreement',
        'privacyAgreement' => 'Privacy Policy Agreement',
        'dataProcessing' => 'Data Processing Consent',
        'newsletter' => 'Newsletter Subscription'
    ];
    
    return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
}

function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round(($bytes / pow($k, $i)), 2) . ' ' . $sizes[$i];
}

$formData = $submissionData['data'] ?? [];
$uploadedFiles = $submissionData['uploaded_files'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Successful | Registration Portal</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .success-page {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
        
        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: bounceIn 1s ease-out;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        
        .success-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .success-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .submission-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            margin: 2rem auto;
            max-width: 600px;
            backdrop-filter: blur(10px);
        }
        
        .submission-id {
            font-size: 1.5rem;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        
        .submission-time {
            opacity: 0.8;
        }
        
        .application-details {
            background: white;
            color: var(--gray-700);
            margin: 2rem 0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }
        
        .section-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-content {
            padding: 2rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 0.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 1rem;
            color: var(--gray-800);
            line-height: 1.5;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .files-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-200);
        }
        
        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .file-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .file-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .file-details h4 {
            margin: 0;
            font-size: 1rem;
            color: var(--gray-800);
        }
        
        .file-details p {
            margin: 0;
            font-size: 0.875rem;
            color: var(--gray-500);
        }
        
        .next-steps {
            background: linear-gradient(135deg, var(--info-color), #0284c7);
            color: white;
            padding: 2rem;
            margin: 2rem 0;
            border-radius: 1rem;
        }
        
        .next-steps h3 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .steps-list {
            list-style: none;
            padding: 0;
        }
        
        .steps-list li {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            backdrop-filter: blur(10px);
        }
        
        .step-number {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        
        .btn-print {
            background: var(--gray-600);
            color: white;
        }
        
        .btn-print:hover {
            background: var(--gray-700);
        }
        
        .btn-email {
            background: var(--info-color);
            color: white;
        }
        
        .btn-email:hover {
            background: #0284c7;
        }
        
        .btn-new {
            background: var(--success-color);
            color: white;
        }
        
        .btn-new:hover {
            background: #047857;
        }
        
        .contact-support {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            margin: 2rem 0;
        }
        
        .support-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .contact-support h3 {
            color: var(--gray-800);
            margin-bottom: 1rem;
        }
        
        .contact-info {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        
        .contact-method {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
        }
        
        @media (max-width: 768px) {
            .success-header h1 {
                font-size: 2rem;
            }
            
            .success-subtitle {
                font-size: 1rem;
            }
            
            .submission-id {
                font-size: 1.25rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }
            
            .contact-info {
                flex-direction: column;
                gap: 1rem;
            }
        }
        
        @media print {
            .success-page,
            .next-steps,
            .action-buttons,
            .contact-support {
                display: none !important;
            }
            
            .application-details {
                box-shadow: none;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Success Header -->
        <div class="success-page">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="success-header">
                <h1>Application Submitted Successfully!</h1>
                <p class="success-subtitle">Thank you for your registration. Your application has been received and is being processed.</p>
            </div>
            
            <div class="submission-info">
                <div class="submission-id">
                    <?php echo htmlspecialchars($submissionData['id']); ?>
                </div>
                <div class="submission-time">
                    Submitted on <?php echo date('F j, Y \a\t g:i A', strtotime($submissionData['timestamp'])); ?>
                </div>
            </div>
        </div>

        <!-- Application Details -->
        <div class="application-details">
            <!-- Personal Information Section -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-user"></i>
                    Personal Information
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        <?php
                        $personalFields = ['firstName', 'lastName', 'dateOfBirth', 'gender', 'nationality', 'idNumber'];
                        foreach ($personalFields as $field) {
                            if (isset($formData[$field]) && !empty($formData[$field])) {
                                echo "<div class='info-item'>";
                                echo "<div class='info-label'>" . getFieldLabel($field) . "</div>";
                                echo "<div class='info-value'>" . formatFieldValue($field, $formData[$field]) . "</div>";
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-address-book"></i>
                    Contact Information
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        <?php
                        $contactFields = ['email', 'phone', 'alternatePhone', 'address', 'city', 'state', 'zipCode', 'country'];
                        foreach ($contactFields as $field) {
                            if (isset($formData[$field]) && !empty($formData[$field])) {
                                echo "<div class='info-item'>";
                                echo "<div class='info-label'>" . getFieldLabel($field) . "</div>";
                                echo "<div class='info-value'>" . formatFieldValue($field, $formData[$field]) . "</div>";
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Education & Professional Section -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-graduation-cap"></i>
                    Education & Professional Information
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        <?php
                        $educationFields = ['education', 'fieldOfStudy', 'institution', 'graduationYear', 'workExperience', 'currentPosition'];
                        foreach ($educationFields as $field) {
                            if (isset($formData[$field]) && !empty($formData[$field])) {
                                echo "<div class='info-item'>";
                                echo "<div class='info-label'>" . getFieldLabel($field) . "</div>";
                                echo "<div class='info-value'>" . formatFieldValue($field, $formData[$field]) . "</div>";
                                echo "</div>";
                            }
                        }
                        
                        // Skills (full width)
                        if (isset($formData['skills']) && !empty($formData['skills'])) {
                            echo "<div class='info-item full-width'>";
                            echo "<div class='info-label'>" . getFieldLabel('skills') . "</div>";
                            echo "<div class='info-value'>" . formatFieldValue('skills', $formData['skills']) . "</div>";
                            echo "</div>";
                        }
                        
                        // Motivation (full width)
                        if (isset($formData['motivation']) && !empty($formData['motivation'])) {
                            echo "<div class='info-item full-width'>";
                            echo "<div class='info-label'>" . getFieldLabel('motivation') . "</div>";
                            echo "<div class='info-value'>" . formatFieldValue('motivation', $formData['motivation']) . "</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                    
                    <!-- Uploaded Files -->
                    <?php if (!empty($uploadedFiles)): ?>
                    <div class="files-section">
                        <h4><i class="fas fa-file-upload"></i> Uploaded Documents</h4>
                        <?php foreach ($uploadedFiles as $file): ?>
                        <div class="file-item">
                            <div class="file-info">
                                <div class="file-icon">
                                    <i class="fas fa-file-<?php echo strpos($file['file_type'], 'pdf') !== false ? 'pdf' : 'alt'; ?>"></i>
                                </div>
                                <div class="file-details">
                                    <h4><?php echo htmlspecialchars($file['original_name']); ?></h4>
                                    <p><?php echo formatFileSize($file['file_size']); ?> • Uploaded <?php echo date('M j, Y g:i A', strtotime($file['upload_time'])); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Agreements Section -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-shield-alt"></i>
                    Agreements & Consents
                </div>
                <div class="section-content">
                    <div class="info-grid">
                        <?php
                        $agreementFields = ['termsAgreement', 'privacyAgreement', 'dataProcessing', 'newsletter'];
                        foreach ($agreementFields as $field) {
                            if (isset($formData[$field])) {
                                echo "<div class='info-item'>";
                                echo "<div class='info-label'>" . getFieldLabel($field) . "</div>";
                                echo "<div class='info-value'>" . formatFieldValue($field, $formData[$field]) . "</div>";
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
            <h3><i class="fas fa-map-signs"></i> What Happens Next?</h3>
            <ol class="steps-list">
                <li>
                    <div class="step-number">1</div>
                    <div>
                        <strong>Application Review</strong><br>
                        Our team will review your application within 3-5 business days.
                    </div>
                </li>
                <li>
                    <div class="step-number">2</div>
                    <div>
                        <strong>Email Notification</strong><br>
                        You'll receive an email update about your application status.
                    </div>
                </li>
                <li>
                    <div class="step-number">3</div>
                    <div>
                        <strong>Further Instructions</strong><br>
                        If approved, you'll receive detailed next steps and requirements.
                    </div>
                </li>
                <li>
                    <div class="step-number">4</div>
                    <div>
                        <strong>Stay Connected</strong><br>
                        Keep your email and phone number updated for important communications.
                    </div>
                </li>
            </ol>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i> Print Application
            </button>
            <button onclick="sendEmailCopy()" class="btn btn-email">
                <i class="fas fa-envelope"></i> Email Copy
            </button>
            <a href="index.html" class="btn btn-new">
                <i class="fas fa-plus"></i> Submit New Application
            </a>
        </div>

        <!-- Contact Support -->
        <div class="contact-support">
            <div class="support-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3>Need Help or Have Questions?</h3>
            <p>Our support team is here to help you with any questions about your application.</p>
            <div class="contact-info">
                <div class="contact-method">
                    <i class="fas fa-envelope"></i>
                    <span>support@registrationportal.com</span>
                </div>
                <div class="contact-method">
                    <i class="fas fa-phone"></i>
                    <span>+1 (555) 123-4567</span>
                </div>
                <div class="contact-method">
                    <i class="fas fa-clock"></i>
                    <span>Mon-Fri, 9 AM - 6 PM EST</span>
                </div>
            </div>
            <p><strong>Reference ID:</strong> <?php echo htmlspecialchars($submissionData['id']); ?></p>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> Registration Portal. All rights reserved.</p>
                <div class="footer-links">
                    <a href="#"><i class="fas fa-file-contract"></i> Terms</a>
                    <a href="#"><i class="fas fa-shield-alt"></i> Privacy</a>
                    <a href="#"><i class="fas fa-question-circle"></i> Help</a>
                    <a href="#"><i class="fas fa-phone"></i> Contact</a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Email copy functionality
        function sendEmailCopy() {
            const submissionId = '<?php echo htmlspecialchars($submissionData['id']); ?>';
            const email = '<?php echo htmlspecialchars($formData['email']); ?>';
            
            // Show loading
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            button.disabled = true;
            
            // Simulate email sending (replace with actual AJAX call to email endpoint)
            setTimeout(function() {
                button.innerHTML = '<i class="fas fa-check"></i> Email Sent!';
                button.classList.add('btn-success');
                
                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    button.classList.remove('btn-success');
                }, 3000);
            }, 2000);
            
            // In a real implementation, you would make an AJAX call like this:
            /*
            $.ajax({
                url: 'send_copy.php',
                method: 'POST',
                data: {
                    submissionId: submissionId,
                    email: email
                },
                success: function(response) {
                    if (response.success) {
                        button.innerHTML = '<i class="fas fa-check"></i> Email Sent!';
                        button.classList.add('btn-success');
                    } else {
                        button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Send Failed';
                        button.classList.add('btn-error');
                    }
                },
                error: function() {
                    button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Send Failed';
                    button.classList.add('btn-error');
                },
                complete: function() {
                    setTimeout(function() {
                        button.innerHTML = originalText;
                        button.disabled = false;
                        button.classList.remove('btn-success', 'btn-error');
                    }, 3000);
                }
            });
            */
        }
        
        // Smooth scroll to sections
        $(document).ready(function() {
            // Add smooth scrolling animations
            $('.info-item').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
                $(this).addClass('fade-in');
            });
        });
    </script>
    
    <style>
        .fade-in {
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .btn-success {
            background: var(--success-color) !important;
        }
        
        .btn-error {
            background: var(--error-color) !important;
        }
    </style>
</body>
</html>