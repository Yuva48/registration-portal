/**
 * Professional Registration Portal JavaScript
 * jQuery-based Multi-Step Form with Validation
 * Author: Registration Portal Team
 * Version: 1.0.0
 */

$(document).ready(function() {
    // ========================================
    // Global Variables
    // ========================================
    let currentStep = 1;
    const totalSteps = 4;
    const formData = {};

    // ========================================
    // Initialization
    // ========================================
    initializeForm();
    setupEventListeners();
    populateGraduationYears();

    function initializeForm() {
        showStep(1);
        updateProgressBar();
        updateStepIndicators();
    }

    // ========================================
    // Event Listeners
    // ========================================
    function setupEventListeners() {
        // Navigation buttons
        $('#nextBtn').on('click', handleNext);
        $('#prevBtn').on('click', handlePrevious);
        $('#submitBtn').on('click', handleSubmit);

        // Form inputs - real-time validation
        $('input, select, textarea').on('blur', function() {
            validateField($(this));
        });

        // Form inputs - clear errors on focus
        $('input, select, textarea').on('focus', function() {
            clearFieldError($(this));
        });

        // Step navigation via progress indicators
        $('.step').on('click', function() {
            const stepNumber = parseInt($(this).data('step'));
            if (stepNumber < currentStep || validateCurrentStep()) {
                goToStep(stepNumber);
            }
        });

        // File upload handling
        $('#documents').on('change', handleFileUpload);

        // Terms and privacy links
        $('.terms-link, .privacy-link').on('click', function(e) {
            e.preventDefault();
            showModal('Terms and Conditions', getTermsContent());
        });

        // Keyboard navigation
        $(document).on('keydown', function(e) {
            if (e.key === 'Enter' && !$(e.target).is('textarea')) {
                e.preventDefault();
                if (currentStep < totalSteps) {
                    handleNext();
                } else {
                    handleSubmit();
                }
            }
        });
    }

    // ========================================
    // Step Navigation
    // ========================================
    function handleNext() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                if (currentStep === totalSteps) {
                    generateReview();
                }
                showStep(currentStep);
                updateProgressBar();
                updateStepIndicators();
                updateNavigationButtons();
                scrollToTop();
            }
        }
    }

    function handlePrevious() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
            updateProgressBar();
            updateStepIndicators();
            updateNavigationButtons();
            scrollToTop();
        }
    }

    function goToStep(stepNumber) {
        if (stepNumber >= 1 && stepNumber <= totalSteps) {
            currentStep = stepNumber;
            if (currentStep === totalSteps) {
                generateReview();
            }
            showStep(currentStep);
            updateProgressBar();
            updateStepIndicators();
            updateNavigationButtons();
            scrollToTop();
        }
    }

    function showStep(step) {
        $('.form-section').removeClass('active').hide();
        $(`#step${step}`).addClass('active').fadeIn(300);
    }

    function updateProgressBar() {
        const progress = (currentStep / totalSteps) * 100;
        $('#progressFill').css('width', progress + '%');
    }

    function updateStepIndicators() {
        $('.step').removeClass('active completed');

        for (let i = 1; i <= currentStep; i++) {
            if (i === currentStep) {
                $(`.step[data-step="${i}"]`).addClass('active');
            } else if (i < currentStep) {
                $(`.step[data-step="${i}"]`).addClass('completed');
            }
        }
    }

    function updateNavigationButtons() {
        // Previous button
        if (currentStep === 1) {
            $('#prevBtn').hide();
        } else {
            $('#prevBtn').show();
        }

        // Next/Submit buttons
        if (currentStep === totalSteps) {
            $('#nextBtn').hide();
            $('#submitBtn').show();
        } else {
            $('#nextBtn').show();
            $('#submitBtn').hide();
        }
    }

    function scrollToTop() {
        $('html, body').animate({
            scrollTop: $('.main-content').offset().top - 100
        }, 500);
    }

    // ========================================
    // Form Validation
    // ========================================
    function validateCurrentStep() {
        let isValid = true;
        const currentStepElement = $(`#step${currentStep}`);

        currentStepElement.find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });

        // Special validation for step 4 (agreements)
        if (currentStep === 4) {
            const requiredCheckboxes = ['termsAgreement', 'privacyAgreement', 'dataProcessing'];
            requiredCheckboxes.forEach(function(id) {
                const checkbox = $(`#${id}`);
                if (!checkbox.is(':checked')) {
                    showFieldError(checkbox, 'This agreement is required');
                    isValid = false;
                }
            });
        }

        return isValid;
    }

    function validateField($field) {
        const fieldType = $field.attr('type') || $field.prop('tagName').toLowerCase();
        const value = $field.val().trim();
        const fieldName = $field.attr('name');
        let isValid = true;
        let errorMessage = '';

        // Clear previous error
        clearFieldError($field);

        // Required field validation
        if ($field.attr('required') && !value) {
            errorMessage = `${getFieldLabel($field)} is required`;
            isValid = false;
        }

        // Type-specific validation
        if (value && isValid) {
            switch (fieldType) {
                case 'email':
                    if (!isValidEmail(value)) {
                        errorMessage = 'Please enter a valid email address';
                        isValid = false;
                    }
                    break;

                case 'tel':
                    if (!isValidPhone(value)) {
                        errorMessage = 'Please enter a valid phone number';
                        isValid = false;
                    }
                    break;

                case 'date':
                    if (!isValidDate(value)) {
                        errorMessage = 'Please enter a valid date';
                        isValid = false;
                    } else if (fieldName === 'dateOfBirth' && !isValidAge(value)) {
                        errorMessage = 'You must be at least 16 years old';
                        isValid = false;
                    }
                    break;

                case 'text':
                    if (fieldName === 'firstName' || fieldName === 'lastName') {
                        if (!isValidName(value)) {
                            errorMessage = 'Name should only contain letters and spaces';
                            isValid = false;
                        }
                    } else if (fieldName === 'zipCode') {
                        if (!isValidZipCode(value)) {
                            errorMessage = 'Please enter a valid ZIP/Postal code';
                            isValid = false;
                        }
                    } else if (fieldName === 'idNumber') {
                        if (value.length < 5) {
                            errorMessage = 'ID/Passport number must be at least 5 characters';
                            isValid = false;
                        }
                    }
                    break;

                case 'textarea':
                    if (fieldName === 'motivation' && value.length < 50) {
                        errorMessage = 'Please provide at least 50 characters for your motivation';
                        isValid = false;
                    }
                    break;
            }
        }

        // Show error or success state
        if (!isValid) {
            showFieldError($field, errorMessage);
        } else if (value) {
            showFieldSuccess($field);
        }

        return isValid;
    }

    // ========================================
    // Validation Helper Functions
    // ========================================
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[\d\s\-\(\)]{10,}$/;
        return phoneRegex.test(phone);
    }

    function isValidDate(date) {
        const parsedDate = new Date(date);
        return !isNaN(parsedDate.getTime());
    }

    function isValidAge(dateOfBirth) {
        const birthDate = new Date(dateOfBirth);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            return age - 1 >= 16;
        }
        return age >= 16;
    }

    function isValidName(name) {
        const nameRegex = /^[a-zA-Z\s\-\.\']+$/;
        return nameRegex.test(name) && name.length >= 2;
    }

    function isValidZipCode(zipCode) {
        const zipRegex = /^[\d\w\s\-]{3,10}$/;
        return zipRegex.test(zipCode);
    }

    // ========================================
    // Error Handling
    // ========================================
    function showFieldError($field, message) {
        const $formGroup = $field.closest('.form-group');
        const $errorMessage = $formGroup.find('.error-message');

        $formGroup.addClass('error').removeClass('success');
        $errorMessage.text(message).addClass('show');

        // Add shake animation
        $field.addClass('shake');
        setTimeout(() => $field.removeClass('shake'), 500);
    }

    function showFieldSuccess($field) {
        const $formGroup = $field.closest('.form-group');
        $formGroup.addClass('success').removeClass('error');
        $formGroup.find('.error-message').removeClass('show');
    }

    function clearFieldError($field) {
        const $formGroup = $field.closest('.form-group');
        $formGroup.removeClass('error success');
        $formGroup.find('.error-message').removeClass('show');
    }

    function getFieldLabel($field) {
        const label = $field.closest('.form-group').find('label').text().replace('*', '').trim();
        return label || $field.attr('name');
    }

    // ========================================
    // Review Generation
    // ========================================
    function generateReview() {
        collectFormData();
        const reviewContent = $('#reviewContent');
        reviewContent.empty();

        const sections = [{
                title: 'Personal Information',
                fields: [
                    { key: 'firstName', label: 'First Name' },
                    { key: 'lastName', label: 'Last Name' },
                    { key: 'dateOfBirth', label: 'Date of Birth' },
                    { key: 'gender', label: 'Gender' },
                    { key: 'nationality', label: 'Nationality' },
                    { key: 'idNumber', label: 'ID/Passport Number' }
                ]
            },
            {
                title: 'Contact Information',
                fields: [
                    { key: 'email', label: 'Email Address' },
                    { key: 'phone', label: 'Phone Number' },
                    { key: 'address', label: 'Street Address' },
                    { key: 'city', label: 'City' },
                    { key: 'state', label: 'State/Province' },
                    { key: 'zipCode', label: 'ZIP/Postal Code' },
                    { key: 'country', label: 'Country' }
                ]
            },
            {
                title: 'Education & Professional',
                fields: [
                    { key: 'education', label: 'Education Level' },
                    { key: 'fieldOfStudy', label: 'Field of Study' },
                    { key: 'institution', label: 'Institution' },
                    { key: 'graduationYear', label: 'Graduation Year' },
                    { key: 'workExperience', label: 'Work Experience' },
                    { key: 'currentPosition', label: 'Current Position' },
                    { key: 'skills', label: 'Skills' },
                    { key: 'motivation', label: 'Motivation' }
                ]
            }
        ];

        sections.forEach(function(section) {
            const sectionDiv = $('<div>').addClass('review-section-group');
            const sectionTitle = $('<h4>').text(section.title).addClass('review-section-title');
            sectionDiv.append(sectionTitle);

            section.fields.forEach(function(field) {
                const value = formData[field.key];
                if (value && value.trim()) {
                    const reviewItem = $('<div>').addClass('review-item');
                    const label = $('<span>').addClass('review-label').text(field.label);
                    let displayValue = value;

                    // Format specific fields
                    if (field.key === 'dateOfBirth') {
                        displayValue = new Date(value).toLocaleDateString();
                    } else if (field.key === 'motivation' && value.length > 100) {
                        displayValue = value.substring(0, 100) + '...';
                    }

                    const valueSpan = $('<span>').addClass('review-value').text(displayValue);
                    reviewItem.append(label, valueSpan);
                    sectionDiv.append(reviewItem);
                }
            });

            reviewContent.append(sectionDiv);
        });

        // Add file upload info
        const fileInput = $('#documents')[0];
        if (fileInput.files && fileInput.files.length > 0) {
            const filesSection = $('<div>').addClass('review-section-group');
            const filesTitle = $('<h4>').text('Uploaded Documents').addClass('review-section-title');
            filesSection.append(filesTitle);

            Array.from(fileInput.files).forEach(function(file) {
                const fileItem = $('<div>').addClass('review-item');
                const fileName = $('<span>').addClass('review-label').text('Document');
                const fileInfo = $('<span>').addClass('review-value').text(`${file.name} (${formatFileSize(file.size)})`);
                fileItem.append(fileName, fileInfo);
                filesSection.append(fileItem);
            });

            reviewContent.append(filesSection);
        }
    }

    function collectFormData() {
        $('#registrationForm').find('input, select, textarea').each(function() {
            const $field = $(this);
            const name = $field.attr('name');
            if (name && $field.attr('type') !== 'file') {
                if ($field.attr('type') === 'checkbox') {
                    formData[name] = $field.is(':checked');
                } else {
                    formData[name] = $field.val();
                }
            }
        });
    }

    // ========================================
    // File Upload Handling
    // ========================================
    function handleFileUpload() {
        const fileInput = this;
        const files = fileInput.files;
        const maxFileSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/jpg', 'image/png'];

        let hasError = false;
        Array.from(files).forEach(function(file) {
            if (file.size > maxFileSize) {
                showFieldError($(fileInput), `File "${file.name}" is too large. Maximum size is 10MB.`);
                hasError = true;
            } else if (!allowedTypes.includes(file.type)) {
                showFieldError($(fileInput), `File "${file.name}" has an unsupported format.`);
                hasError = true;
            }
        });

        if (hasError) {
            fileInput.value = '';
        } else if (files.length > 0) {
            showFieldSuccess($(fileInput));
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // ========================================
    // Form Submission
    // ========================================
    function handleSubmit(e) {
        e.preventDefault();

        if (!validateCurrentStep()) {
            return;
        }

        // Show loading overlay
        showLoadingOverlay();

        // Submit form normally (no AJAX)
        const formElement = document.getElementById('registrationForm');
        formElement.action = 'process-simple.php';
        formElement.method = 'POST';
        formElement.submit();
    }

    function showLoadingOverlay() {
        $('#loadingOverlay').fadeIn(300);
        $('body').addClass('loading');
    }

    function hideLoadingOverlay() {
        $('#loadingOverlay').fadeOut(300);
        $('body').removeClass('loading');
    }

    // ========================================
    // Utility Functions
    // ========================================
    function populateGraduationYears() {
        const currentYear = new Date().getFullYear();
        const $graduationYear = $('#graduationYear');

        for (let year = currentYear + 5; year >= currentYear - 50; year--) {
            $graduationYear.append(new Option(year, year));
        }
    }

    function showModal(title, content) {
        const modal = $(`
            <div class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>${title}</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        ${content}
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary modal-ok">OK</button>
                    </div>
                </div>
            </div>
        `);

        $('body').append(modal);
        modal.fadeIn(300);

        modal.find('.modal-close, .modal-ok').on('click', function() {
            modal.fadeOut(300, function() {
                modal.remove();
            });
        });

        modal.on('click', function(e) {
            if (e.target === this) {
                modal.fadeOut(300, function() {
                    modal.remove();
                });
            }
        });
    }

    function showErrorModal(title, message) {
        showModal(title, `<div class="error-content"><i class="fas fa-exclamation-triangle"></i><p>${message}</p></div>`);
    }

    function getTermsContent() {
        return `
            <div class="terms-content">
                <h4>Terms and Conditions</h4>
                <p>By using this registration portal, you agree to the following terms:</p>
                <ul>
                    <li>All information provided must be accurate and truthful</li>
                    <li>You are responsible for keeping your information up to date</li>
                    <li>We reserve the right to verify all submitted information</li>
                    <li>Applications may be rejected for incomplete or inaccurate information</li>
                    <li>You will be notified of the application status via email</li>
                </ul>
                
                <h4>Privacy Policy</h4>
                <p>We are committed to protecting your privacy:</p>
                <ul>
                    <li>Your personal information is encrypted and stored securely</li>
                    <li>Information is only used for application processing purposes</li>
                    <li>We do not share your information with third parties without consent</li>
                    <li>You have the right to request deletion of your data</li>
                    <li>Contact us for any privacy-related concerns</li>
                </ul>
            </div>
        `;
    }

    // ========================================
    // Auto-save functionality (Optional)
    // ========================================
    function autoSave() {
        collectFormData();
        localStorage.setItem('registrationFormData', JSON.stringify(formData));
    }

    function loadSavedData() {
        const savedData = localStorage.getItem('registrationFormData');
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(function(key) {
                    const $field = $(`[name="${key}"]`);
                    if ($field.length) {
                        if ($field.attr('type') === 'checkbox') {
                            $field.prop('checked', data[key]);
                        } else {
                            $field.val(data[key]);
                        }
                    }
                });

                // Show message about loaded data
                if (Object.keys(data).length > 0) {
                    showModal('Data Restored', 'Your previously entered information has been restored. You can continue where you left off.');
                }
            } catch (error) {
                console.error('Error loading saved data:', error);
            }
        }
    }

    // Auto-save on form change
    $('#registrationForm').on('change', 'input, select, textarea', function() {
        setTimeout(autoSave, 1000); // Save after 1 second
    });

    // Load saved data on page load
    setTimeout(loadSavedData, 500);

    // Clear saved data on successful submission
    $(window).on('beforeunload', function() {
        if (formData.submitted) {
            localStorage.removeItem('registrationFormData');
        }
    });

    // ========================================
    // Additional Enhancements
    // ========================================

    // Character counter for textarea fields
    $('textarea').each(function() {
        const $textarea = $(this);
        const maxLength = $textarea.attr('maxlength');
        if (maxLength) {
            const $counter = $('<div class="char-counter"></div>');
            $textarea.after($counter);

            $textarea.on('input', function() {
                const remaining = maxLength - $(this).val().length;
                $counter.text(`${remaining} characters remaining`);
                $counter.toggleClass('warning', remaining < 50);
            });

            $textarea.trigger('input');
        }
    });

    // Smooth scrolling for form navigation
    function smoothScrollToElement($element) {
        $('html, body').animate({
            scrollTop: $element.offset().top - 100
        }, 500);
    }

    // Tooltip for help icons (if added)
    $('[data-tooltip]').each(function() {
        const $element = $(this);
        const tooltipText = $element.data('tooltip');

        $element.on('mouseenter', function() {
            const tooltip = $(`<div class="tooltip">${tooltipText}</div>`);
            $('body').append(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.css({
                top: rect.bottom + 10,
                left: rect.left + (rect.width / 2) - (tooltip.width() / 2)
            });
        });

        $element.on('mouseleave', function() {
            $('.tooltip').remove();
        });
    });

    // Form validation on paste
    $('input').on('paste', function() {
        const $this = $(this);
        setTimeout(function() {
            validateField($this);
        }, 100);
    });

    // Prevent multiple form submissions
    let isSubmitting = false;
    $('#registrationForm').on('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        isSubmitting = true;
    });
});

// ========================================
// CSS for dynamic elements
// ========================================
$(document).ready(function() {
    $('<style>').text(`
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 20%, 40%, 60%, 80% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-5px);
            }
        }
        
        .char-counter {
            font-size: 0.875rem;
            color: #64748b;
            text-align: right;
            margin-top: 0.25rem;
        }
        
        .char-counter.warning {
            color: #d97706;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(4px);
        }
        
        .modal-content {
            background: white;
            border-radius: 1rem;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
            color: #64748b;
        }
        
        .modal-close:hover {
            color: #1e293b;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e2e8f0;
            text-align: right;
        }
        
        .error-content {
            text-align: center;
            padding: 1rem;
        }
        
        .error-content i {
            font-size: 3rem;
            color: #dc2626;
            margin-bottom: 1rem;
        }
        
        .terms-content {
            line-height: 1.6;
        }
        
        .terms-content h4 {
            color: #1e293b;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .terms-content ul {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }
        
        .terms-content li {
            margin: 0.5rem 0;
        }
        
        .review-section-group {
            margin-bottom: 1.5rem;
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .review-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .tooltip {
            position: absolute;
            background: #1e293b;
            color: white;
            padding: 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            z-index: 1000;
            white-space: nowrap;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .tooltip::before {
            content: '';
            position: absolute;
            top: -5px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 5px solid #1e293b;
        }
        
        body.loading {
            overflow: hidden;
        }
    `).appendTo('head');
});