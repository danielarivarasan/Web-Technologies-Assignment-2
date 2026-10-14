/**
 * ==============================================================================
 * Web Technology Assignment II - Question 1 JavaScript
 * Student Registration Form Client-Side Validation and Dynamic Interactions
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
  // Form and Field Elements
  const form = document.getElementById('studentRegistrationForm');
  const fullName = document.getElementById('fullName');
  const rollNumber = document.getElementById('rollNumber');
  const email = document.getElementById('email');
  const course = document.getElementById('course');
  const fileUpload = document.getElementById('fileUpload');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirmPassword');
  const terms = document.getElementById('terms');

  // Password Toggle Buttons
  const togglePasswordBtn = document.getElementById('togglePasswordBtn');
  const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPasswordBtn');

  // Password Strength Meter Elements
  const strengthContainer = document.getElementById('strengthContainer');
  const strengthBarFill = document.getElementById('strengthBarFill');
  const strengthText = document.getElementById('strengthText');

  // File Dropzone & Preview Elements
  const fileDropzone = document.getElementById('fileDropzone');
  const filePreview = document.getElementById('filePreview');
  const fileNameDisplay = document.getElementById('fileNameDisplay');
  const fileSizeDisplay = document.getElementById('fileSizeDisplay');
  const removeFileBtn = document.getElementById('removeFileBtn');

  // Success Modal Elements
  const successModal = document.getElementById('successModal');
  const summaryDetails = document.getElementById('summaryDetails');
  const closeModalBtn = document.getElementById('closeModalBtn');

  // Allowed file settings
  const MAX_FILE_SIZE_BYTES = 2 * 1024 * 1024; // 2 MB
  const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];
  const ALLOWED_EXTENSIONS = ['.jpg', '.jpeg', '.png', '.pdf'];

  // ----------------------------------------------------------------------------
  // Helper Functions: UI Validation State Handlers
  // ----------------------------------------------------------------------------
  function setError(input, errorElementId, message) {
    const group = input.closest('.form-group');
    const errorEl = document.getElementById(errorElementId);
    if (group) {
      group.classList.remove('is-valid');
      group.classList.add('has-error');
    }
    if (errorEl) {
      errorEl.textContent = message;
    }
    input.setAttribute('aria-invalid', 'true');
  }

  function setSuccess(input, errorElementId) {
    const group = input.closest('.form-group');
    const errorEl = document.getElementById(errorElementId);
    if (group) {
      group.classList.remove('has-error');
      group.classList.add('is-valid');
    }
    if (errorEl) {
      errorEl.textContent = '';
    }
    input.setAttribute('aria-invalid', 'false');
  }

  function clearState(input, errorElementId) {
    const group = input.closest('.form-group');
    const errorEl = document.getElementById(errorElementId);
    if (group) {
      group.classList.remove('has-error', 'is-valid');
    }
    if (errorEl) {
      errorEl.textContent = '';
    }
    input.removeAttribute('aria-invalid');
  }

  // ----------------------------------------------------------------------------
  // Field Validators
  // ----------------------------------------------------------------------------

  // 1. Full Name Validation: Alphabets and spaces only, 3-50 chars
  function validateFullName() {
    const value = fullName.value.trim();
    const nameRegex = /^[A-Za-z\s]{3,50}$/;

    if (!value) {
      setError(fullName, 'fullNameError', 'Full name is required.');
      return false;
    }
    if (value.length < 3) {
      setError(fullName, 'fullNameError', 'Name must be at least 3 characters long.');
      return false;
    }
    if (!nameRegex.test(value)) {
      setError(fullName, 'fullNameError', 'Full name can only contain alphabetic letters and single spaces.');
      return false;
    }
    setSuccess(fullName, 'fullNameError');
    return true;
  }

  // 2. Roll Number Validation: Alphanumeric and hyphens, 3-20 chars
  function validateRollNumber() {
    const value = rollNumber.value.trim();
    const rollRegex = /^[A-Za-z0-9_-]{3,20}$/;

    if (!value) {
      setError(rollNumber, 'rollNumberError', 'Roll number is required.');
      return false;
    }
    if (!rollRegex.test(value)) {
      setError(rollNumber, 'rollNumberError', 'Roll number must be 3-20 alphanumeric characters (e.g. WT-2026-001).');
      return false;
    }
    setSuccess(rollNumber, 'rollNumberError');
    return true;
  }

  // 3. Email Validation: Valid institutional/personal email format
  function validateEmail() {
    const value = email.value.trim();
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!value) {
      setError(email, 'emailError', 'Email address is required.');
      return false;
    }
    if (!emailRegex.test(value)) {
      setError(email, 'emailError', 'Please enter a valid email address (e.g., student@university.edu).');
      return false;
    }
    setSuccess(email, 'emailError');
    return true;
  }

  // 4. Course Validation: Must select an option
  function validateCourse() {
    const value = course.value;
    if (!value) {
      setError(course, 'courseError', 'Please select an academic course.');
      return false;
    }
    setSuccess(course, 'courseError');
    return true;
  }

  // 5. File Upload Validation: Type and Size Check
  function validateFileUpload() {
    const files = fileUpload.files;
    if (!files || files.length === 0) {
      setError(fileUpload, 'fileUploadError', 'Please select or drop a profile photo or document.');
      return false;
    }

    const file = files[0];
    const fileName = file.name.toLowerCase();
    const hasValidExt = ALLOWED_EXTENSIONS.some(ext => fileName.endsWith(ext));

    if (!hasValidExt) {
      setError(fileUpload, 'fileUploadError', 'Invalid file type. Only JPG, PNG, and PDF files are permitted.');
      return false;
    }

    if (file.size > MAX_FILE_SIZE_BYTES) {
      const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
      setError(fileUpload, 'fileUploadError', `File size (${sizeMb} MB) exceeds maximum allowed limit of 2 MB.`);
      return false;
    }

    setSuccess(fileUpload, 'fileUploadError');
    return true;
  }

  // 6. Password Complexity Validation & Strength Meter
  function evaluatePasswordStrength(val) {
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[a-z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    return score; // Max 5
  }

  function updatePasswordStrengthMeter(val) {
    if (!val) {
      strengthContainer.classList.remove('active');
      return;
    }
    strengthContainer.classList.add('active');
    const score = evaluatePasswordStrength(val);

    let width = '20%';
    let color = '#ef4444'; // Red
    let label = 'Very Weak';

    if (score === 2) {
      width = '40%';
      color = '#f97316'; // Orange
      label = 'Weak';
    } else if (score === 3) {
      width = '65%';
      color = '#eab308'; // Amber
      label = 'Fair';
    } else if (score === 4) {
      width = '85%';
      color = '#3b82f6'; // Blue
      label = 'Good';
    } else if (score >= 5) {
      width = '100%';
      color = '#10b981'; // Green
      label = 'Strong';
    }

    strengthBarFill.style.width = width;
    strengthBarFill.style.backgroundColor = color;
    strengthText.textContent = `Strength: ${label}`;
    strengthText.style.color = color;
  }

  function validatePassword() {
    const value = password.value;
    updatePasswordStrengthMeter(value);

    if (!value) {
      setError(password, 'passwordError', 'Password is required.');
      return false;
    }

    // Minimum 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 symbol
    const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
    if (!strongRegex.test(value)) {
      setError(password, 'passwordError', 'Must be at least 8 characters with 1 uppercase, 1 lowercase, 1 digit, and 1 symbol.');
      return false;
    }

    setSuccess(password, 'passwordError');

    // Also re-validate confirm password if it already has a value
    if (confirmPassword.value) {
      validateConfirmPassword();
    }
    return true;
  }

  // 7. Confirm Password Validation: Exact match
  function validateConfirmPassword() {
    const passVal = password.value;
    const confirmVal = confirmPassword.value;

    if (!confirmVal) {
      setError(confirmPassword, 'confirmPasswordError', 'Please confirm your password.');
      return false;
    }
    if (confirmVal !== passVal) {
      setError(confirmPassword, 'confirmPasswordError', 'Passwords do not match.');
      return false;
    }
    setSuccess(confirmPassword, 'confirmPasswordError');
    return true;
  }

  // 8. Terms Checkbox Validation
  function validateTerms() {
    if (!terms.checked) {
      setError(terms, 'termsError', 'You must agree to the student academic declaration.');
      return false;
    }
    setSuccess(terms, 'termsError');
    return true;
  }

  // ----------------------------------------------------------------------------
  // Event Listeners for Real-time Feedback
  // ----------------------------------------------------------------------------
  fullName.addEventListener('input', validateFullName);
  fullName.addEventListener('blur', validateFullName);

  rollNumber.addEventListener('input', validateRollNumber);
  rollNumber.addEventListener('blur', validateRollNumber);

  email.addEventListener('input', validateEmail);
  email.addEventListener('blur', validateEmail);

  course.addEventListener('change', validateCourse);
  course.addEventListener('blur', validateCourse);

  password.addEventListener('input', validatePassword);
  password.addEventListener('blur', validatePassword);

  confirmPassword.addEventListener('input', validateConfirmPassword);
  confirmPassword.addEventListener('blur', validateConfirmPassword);

  terms.addEventListener('change', validateTerms);

  // File Input Change & Dropzone Preview
  function updateFileDisplay(file) {
    if (file) {
      fileNameDisplay.textContent = file.name;
      const sizeKb = (file.size / 1024).toFixed(1);
      fileSizeDisplay.textContent = `(${sizeKb} KB)`;
      filePreview.style.display = 'flex';
      validateFileUpload();
    } else {
      filePreview.style.display = 'none';
      fileNameDisplay.textContent = '';
      fileSizeDisplay.textContent = '';
    }
  }

  fileUpload.addEventListener('change', (e) => {
    if (e.target.files && e.target.files[0]) {
      updateFileDisplay(e.target.files[0]);
    } else {
      updateFileDisplay(null);
    }
  });

  removeFileBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    fileUpload.value = '';
    updateFileDisplay(null);
    clearState(fileUpload, 'fileUploadError');
  });

  // Drag and drop events for file dropzone
  ['dragenter', 'dragover'].forEach(eventName => {
    fileDropzone.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      fileDropzone.classList.add('dragover');
    });
  });

  ['dragleave', 'drop'].forEach(eventName => {
    fileDropzone.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      fileDropzone.classList.remove('dragover');
    });
  });

  fileDropzone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    if (dt && dt.files && dt.files.length > 0) {
      fileUpload.files = dt.files;
      updateFileDisplay(dt.files[0]);
    }
  });

  // Password Visibility Toggles
  function setupPasswordToggle(btn, inputField) {
    btn.addEventListener('click', () => {
      const isPassword = inputField.type === 'password';
      inputField.type = isPassword ? 'text' : 'password';
      btn.textContent = isPassword ? '🙈' : '👁️';
    });
  }
  setupPasswordToggle(togglePasswordBtn, password);
  setupPasswordToggle(toggleConfirmPasswordBtn, confirmPassword);

  // Form Reset Handling
  form.addEventListener('reset', () => {
    setTimeout(() => {
      [fullName, rollNumber, email, course, fileUpload, password, confirmPassword, terms].forEach(field => {
        clearState(field, `${field.id}Error`);
      });
      updateFileDisplay(null);
      strengthContainer.classList.remove('active');
    }, 10);
  });

  // ----------------------------------------------------------------------------
  // Form Submit Handling: Full Validation Check
  // ----------------------------------------------------------------------------
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const isNameValid = validateFullName();
    const isRollValid = validateRollNumber();
    const isEmailValid = validateEmail();
    const isCourseValid = validateCourse();
    const isFileValid = validateFileUpload();
    const isPassValid = validatePassword();
    const isConfirmValid = validateConfirmPassword();
    const isTermsValid = validateTerms();

    const isFormValid = isNameValid && isRollValid && isEmailValid && isCourseValid &&
                        isFileValid && isPassValid && isConfirmValid && isTermsValid;

    if (!isFormValid) {
      // Find first invalid input and focus it
      const firstInvalidGroup = form.querySelector('.form-group.has-error input, .form-group.has-error select');
      if (firstInvalidGroup) {
        firstInvalidGroup.focus();
      }
      return;
    }

    // Populate and show success confirmation modal
    const uploadedFile = fileUpload.files[0];
    summaryDetails.innerHTML = `
      <div class="summary-item">
        <span class="summary-label">Student Name:</span>
        <span class="summary-value">${escapeHtml(fullName.value.trim())}</span>
      </div>
      <div class="summary-item">
        <span class="summary-label">Roll Number:</span>
        <span class="summary-value">${escapeHtml(rollNumber.value.trim())}</span>
      </div>
      <div class="summary-item">
        <span class="summary-label">Email Address:</span>
        <span class="summary-value">${escapeHtml(email.value.trim())}</span>
      </div>
      <div class="summary-item">
        <span class="summary-label">Selected Course:</span>
        <span class="summary-value">${escapeHtml(course.value)}</span>
      </div>
      <div class="summary-item">
        <span class="summary-label">Attached File:</span>
        <span class="summary-value">${escapeHtml(uploadedFile.name)} (${(uploadedFile.size / 1024).toFixed(1)} KB)</span>
      </div>
      <div class="summary-item">
        <span class="summary-label">Validation Status:</span>
        <span class="summary-value" style="color: #10b981; font-weight: 600;">Verified & Client-Validated</span>
      </div>
    `;

    successModal.style.display = 'flex';
  });

  // Modal Close & Reset
  closeModalBtn.addEventListener('click', () => {
    successModal.style.display = 'none';
    form.reset();
  });

  // Utility to prevent XSS in modal output
  function escapeHtml(str) {
    return str.replace(/[&<>'"]/g, 
      tag => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;'
      }[tag] || tag)
    );
  }
});
