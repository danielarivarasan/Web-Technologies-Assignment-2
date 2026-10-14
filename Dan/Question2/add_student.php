<?php
/**
 * ==============================================================================
 * Web Technology Assignment II - Question 2: Add Student Record
 * File: Question2/add_student.php
 * 
 * Inserts new student record with profile image & document uploads.
 * Demonstrates prepared statements, metadata persistence, and input sanitization.
 * ==============================================================================
 */

session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/upload.php';

$errors = [];
$formData = [
    'roll_number' => '',
    'full_name'   => '',
    'email'       => '',
    'course'      => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitize text inputs
    $formData['roll_number'] = strtoupper(trim($_POST['roll_number'] ?? ''));
    $formData['full_name']   = trim($_POST['full_name'] ?? '');
    $formData['email']       = strtolower(trim($_POST['email'] ?? ''));
    $formData['course']      = trim($_POST['course'] ?? '');

    // 2. Validate Text Inputs
    if (empty($formData['roll_number'])) {
        $errors[] = 'Roll Number is required.';
    } elseif (!preg_match('/^[A-Z0-9_-]{3,20}$/', $formData['roll_number'])) {
        $errors[] = 'Roll Number must be 3-20 alphanumeric characters or hyphens.';
    }

    if (empty($formData['full_name'])) {
        $errors[] = 'Full Name is required.';
    } elseif (strlen($formData['full_name']) < 3) {
        $errors[] = 'Full Name must be at least 3 characters.';
    }

    if (empty($formData['email'])) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address.';
    }

    if (empty($formData['course'])) {
        $errors[] = 'Please select a course.';
    }

    // 3. Check for unique Roll Number and Email using Prepared Statements
    if (empty($errors)) {
        try {
            $checkStmt = $pdo->prepare("SELECT roll_number, email FROM students WHERE roll_number = :roll OR email = :email LIMIT 1");
            $checkStmt->execute([
                ':roll'  => $formData['roll_number'],
                ':email' => $formData['email']
            ]);
            $existing = $checkStmt->fetch();

            if ($existing) {
                if ($existing['roll_number'] === $formData['roll_number']) {
                    $errors[] = "A student with Roll Number '{$formData['roll_number']}' already exists.";
                }
                if ($existing['email'] === $formData['email']) {
                    $errors[] = "A student with Email '{$formData['email']}' already exists.";
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Database verification error: " . $e->getMessage();
        }
    }

    // 4. Handle File Uploads (Profile Image & Document)
    $profileImageMeta = ['filename' => null, 'type' => null, 'size' => 0];
    $documentMeta     = ['filename' => null, 'type' => null, 'size' => 0];

    // Profile Image
    if (empty($errors) && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imgResult = handleFileUpload($_FILES['profile_image'], 'image');
        if ($imgResult['success']) {
            $profileImageMeta['filename'] = $imgResult['filename'];
            $profileImageMeta['type']     = $imgResult['file_type'];
            $profileImageMeta['size']     = $imgResult['file_size'];
        } else {
            $errors[] = "Profile Image Error: " . $imgResult['error'];
        }
    }

    // Student Document
    if (empty($errors) && isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
        $docResult = handleFileUpload($_FILES['document'], 'document');
        if ($docResult['success']) {
            $documentMeta['filename'] = $docResult['filename'];
            $documentMeta['type']     = $docResult['file_type'];
            $documentMeta['size']     = $docResult['file_size'];
        } else {
            $errors[] = "Document Error: " . $docResult['error'];
            // Clean up image if document upload failed
            if ($profileImageMeta['filename']) {
                deleteUploadedFile($profileImageMeta['filename']);
            }
        }
    }

    // 5. Insert into Database using Prepared Statement
    if (empty($errors)) {
        try {
            $insertSql = "INSERT INTO students 
                (roll_number, full_name, email, course, 
                 profile_image, profile_image_type, profile_image_size, 
                 document, document_type, document_size) 
                VALUES 
                (:roll, :name, :email, :course, 
                 :img_name, :img_type, :img_size, 
                 :doc_name, :doc_type, :doc_size)";

            $stmt = $pdo->prepare($insertSql);
            $stmt->execute([
                ':roll'     => $formData['roll_number'],
                ':name'     => $formData['full_name'],
                ':email'    => $formData['email'],
                ':course'   => $formData['course'],
                ':img_name' => $profileImageMeta['filename'],
                ':img_type' => $profileImageMeta['type'],
                ':img_size' => $profileImageMeta['size'],
                ':doc_name' => $documentMeta['filename'],
                ':doc_type' => $documentMeta['type'],
                ':doc_size' => $documentMeta['size']
            ]);

            $_SESSION['flash_success'] = "Student '{$formData['full_name']}' ({$formData['roll_number']}) added successfully!";
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            // Clean up uploaded files if DB insertion throws
            if ($profileImageMeta['filename']) deleteUploadedFile($profileImageMeta['filename']);
            if ($documentMeta['filename']) deleteUploadedFile($documentMeta['filename']);
            $errors[] = "Database insert error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Student | Question 2 (PHP & MySQL)</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <header class="navbar">
      <div class="nav-brand">
        <h2>Add New Student</h2>
        <span class="subtitle">Enroll a student and save verified file metadata to MySQL</span>
      </div>
      <div class="nav-actions">
        <a href="index.php" class="btn btn-secondary">&larr; Back to Student List</a>
      </div>
    </header>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger" style="display: block;">
        <strong>Please correct the following errors:</strong>
        <ul style="margin: 8px 0 0 20px;">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <main class="form-card">
      <h3 class="form-title">Student Information Form</h3>
      <form method="POST" action="add_student.php" enctype="multipart/form-data">
        
        <div class="form-row">
          <div class="form-group">
            <label for="roll_number">Roll Number *</label>
            <input 
              type="text" 
              id="roll_number" 
              name="roll_number" 
              class="form-control" 
              placeholder="e.g. WT-2026-005" 
              value="<?= htmlspecialchars($formData['roll_number']) ?>" 
              required
            >
          </div>

          <div class="form-group">
            <label for="full_name">Full Name *</label>
            <input 
              type="text" 
              id="full_name" 
              name="full_name" 
              class="form-control" 
              placeholder="e.g. Maya Patel" 
              value="<?= htmlspecialchars($formData['full_name']) ?>" 
              required
            >
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="email">Institutional Email *</label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              class="form-control" 
              placeholder="student@university.edu" 
              value="<?= htmlspecialchars($formData['email']) ?>" 
              required
            >
          </div>

          <div class="form-group">
            <label for="course">Academic Program *</label>
            <select id="course" name="course" class="form-control" required>
              <option value="">-- Choose Program --</option>
              <?php 
                $courses = [
                  'Computer Science & Engineering',
                  'Artificial Intelligence & Data Science',
                  'Information Technology',
                  'Software Engineering',
                  'Cybersecurity & Digital Forensics'
                ];
                foreach ($courses as $c): 
              ?>
                <option value="<?= $c ?>" <?= ($formData['course'] === $c) ? 'selected' : '' ?>>
                  <?= $c ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="profile_image">Profile Picture (JPG, PNG, WebP &bull; Max 2MB)</label>
          <input 
            type="file" 
            id="profile_image" 
            name="profile_image" 
            class="form-control" 
            accept="image/jpeg,image/png,image/webp"
          >
          <small class="subtitle">A unique filename will be assigned and metadata recorded in MySQL.</small>
        </div>

        <div class="form-group">
          <label for="document">Verification Document (PDF, DOC, DOCX &bull; Max 5MB)</label>
          <input 
            type="file" 
            id="document" 
            name="document" 
            class="form-control" 
            accept=".pdf,.doc,.docx,application/pdf,application/msword"
          >
          <small class="subtitle">Supports marksheets, identity proof, or assignment submissions.</small>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
          <a href="index.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Student Record</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
