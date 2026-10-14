<?php
/**
 * ==============================================================================
 * Web Technology Assignment II - Question 2: Edit Student Record
 * File: Question2/edit_student.php
 * 
 * Allows updating student information and replacing profile photos / documents.
 * Ensures duplicate validation exclusion and old file cleanup.
 * ==============================================================================
 */

session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/upload.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id && isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id) {
    $_SESSION['flash_error'] = 'Invalid student ID.';
    header('Location: index.php');
    exit;
}

// Fetch existing record
try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $student = $stmt->fetch();

    if (!$student) {
        $_SESSION['flash_error'] = 'Student record does not exist.';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Database query error: " . htmlspecialchars($e->getMessage()));
}

$errors = [];
$formData = [
    'roll_number' => $student['roll_number'],
    'full_name'   => $student['full_name'],
    'email'       => $student['email'],
    'course'      => $student['course']
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

    // 3. Check for unique Roll Number and Email excluding current ID
    if (empty($errors)) {
        try {
            $checkStmt = $pdo->prepare("SELECT roll_number, email FROM students WHERE (roll_number = :roll OR email = :email) AND id != :id LIMIT 1");
            $checkStmt->execute([
                ':roll'  => $formData['roll_number'],
                ':email' => $formData['email'],
                ':id'    => $id
            ]);
            $conflict = $checkStmt->fetch();

            if ($conflict) {
                if ($conflict['roll_number'] === $formData['roll_number']) {
                    $errors[] = "Roll Number '{$formData['roll_number']}' is already assigned to another student.";
                }
                if ($conflict['email'] === $formData['email']) {
                    $errors[] = "Email '{$formData['email']}' is already assigned to another student.";
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Database verification error: " . $e->getMessage();
        }
    }

    // Default to retaining existing file metadata
    $imgFilename = $student['profile_image'];
    $imgType     = $student['profile_image_type'];
    $imgSize     = $student['profile_image_size'];

    $docFilename = $student['document'];
    $docType     = $student['document_type'];
    $docSize     = $student['document_size'];

    $newImgUploaded = false;
    $newDocUploaded = false;

    // 4. Handle Profile Image Replacement if uploaded
    if (empty($errors) && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imgResult = handleFileUpload($_FILES['profile_image'], 'image');
        if ($imgResult['success']) {
            $newImgUploaded = true;
            $newImgFilename = $imgResult['filename'];
            $newImgType     = $imgResult['file_type'];
            $newImgSize     = $imgResult['file_size'];
        } else {
            $errors[] = "Profile Image Error: " . $imgResult['error'];
        }
    }

    // 5. Handle Document Replacement if uploaded
    if (empty($errors) && isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
        $docResult = handleFileUpload($_FILES['document'], 'document');
        if ($docResult['success']) {
            $newDocUploaded = true;
            $newDocFilename = $docResult['filename'];
            $newDocType     = $docResult['file_type'];
            $newDocSize     = $docResult['file_size'];
        } else {
            $errors[] = "Document Error: " . $docResult['error'];
            if ($newImgUploaded) {
                deleteUploadedFile($newImgFilename);
            }
        }
    }

    // 6. Execute Prepared UPDATE Statement
    if (empty($errors)) {
        try {
            if ($newImgUploaded) {
                // Delete old image from disk
                deleteUploadedFile($student['profile_image']);
                $imgFilename = $newImgFilename;
                $imgType     = $newImgType;
                $imgSize     = $newImgSize;
            }

            if ($newDocUploaded) {
                // Delete old document from disk
                deleteUploadedFile($student['document']);
                $docFilename = $newDocFilename;
                $docType     = $newDocType;
                $docSize     = $newDocSize;
            }

            $updateSql = "UPDATE students SET 
                roll_number = :roll,
                full_name = :name,
                email = :email,
                course = :course,
                profile_image = :img_name,
                profile_image_type = :img_type,
                profile_image_size = :img_size,
                document = :doc_name,
                document_type = :doc_type,
                document_size = :doc_size
                WHERE id = :id";

            $stmt = $pdo->prepare($updateSql);
            $stmt->execute([
                ':roll'     => $formData['roll_number'],
                ':name'     => $formData['full_name'],
                ':email'    => $formData['email'],
                ':course'   => $formData['course'],
                ':img_name' => $imgFilename,
                ':img_type' => $imgType,
                ':img_size' => $imgSize,
                ':doc_name' => $docFilename,
                ':doc_type' => $docType,
                ':doc_size' => $docSize,
                ':id'       => $id
            ]);

            $_SESSION['flash_success'] = "Student '{$formData['full_name']}' updated successfully!";
            header("Location: view_student.php?id=" . $id);
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database update error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Student: <?= htmlspecialchars($student['full_name']) ?> | Question 2</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <header class="navbar">
      <div class="nav-brand">
        <h2>Edit Student Record</h2>
        <span class="subtitle">Modify details or update profile documents</span>
      </div>
      <div class="nav-actions">
        <a href="view_student.php?id=<?= $student['id'] ?>" class="btn btn-secondary">&larr; Cancel & View Profile</a>
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
      <h3 class="form-title">Edit Details for <?= htmlspecialchars($student['full_name']) ?></h3>
      <form method="POST" action="edit_student.php?id=<?= $student['id'] ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $student['id'] ?>">

        <div class="form-row">
          <div class="form-group">
            <label for="roll_number">Roll Number *</label>
            <input 
              type="text" 
              id="roll_number" 
              name="roll_number" 
              class="form-control" 
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
              value="<?= htmlspecialchars($formData['email']) ?>" 
              required
            >
          </div>

          <div class="form-group">
            <label for="course">Academic Program *</label>
            <select id="course" name="course" class="form-control" required>
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
          <label for="profile_image">Replace Profile Picture (Optional)</label>
          <input 
            type="file" 
            id="profile_image" 
            name="profile_image" 
            class="form-control" 
            accept="image/jpeg,image/png,image/webp"
          >
          <?php if (!empty($student['profile_image'])): ?>
            <div class="current-file-badge">
              <span>Current file: <strong><?= htmlspecialchars($student['profile_image']) ?></strong></span>
            </div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="document">Replace Verification Document (Optional)</label>
          <input 
            type="file" 
            id="document" 
            name="document" 
            class="form-control" 
            accept=".pdf,.doc,.docx,application/pdf,application/msword"
          >
          <?php if (!empty($student['document'])): ?>
            <div class="current-file-badge">
              <span>Current file: <strong><?= htmlspecialchars($student['document']) ?></strong></span>
            </div>
          <?php endif; ?>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
          <a href="view_student.php?id=<?= $student['id'] ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Update Student Record</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
