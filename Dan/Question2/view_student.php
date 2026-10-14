<?php
/**
 * ==============================================================================
 * Web Technology Assignment II - Question 2: View Student Profile
 * File: Question2/view_student.php
 * 
 * Displays complete student information, uploaded profile photo preview,
 * and document download with full file metadata.
 * ==============================================================================
 */

session_start();
require_once __DIR__ . '/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash_error'] = 'Invalid student ID specified.';
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $student = $stmt->fetch();

    if (!$student) {
        $_SESSION['flash_error'] = 'Student record not found.';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Database query error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Profile: <?= htmlspecialchars($student['full_name']) ?> | Question 2</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <header class="navbar">
      <div class="nav-brand">
        <h2>Student Profile Card</h2>
        <span class="subtitle">Complete record overview &bull; Roll No: <?= htmlspecialchars($student['roll_number']) ?></span>
      </div>
      <div class="nav-actions">
        <a href="index.php" class="btn btn-secondary">&larr; Back to Student List</a>
        <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn btn-primary">✏ Edit Record</a>
      </div>
    </header>

    <main class="profile-card">
      <div class="profile-header">
        <?php if (!empty($student['profile_image']) && file_exists(__DIR__ . '/uploads/' . $student['profile_image'])): ?>
          <img 
            src="uploads/<?= htmlspecialchars($student['profile_image']) ?>" 
            alt="Profile of <?= htmlspecialchars($student['full_name']) ?>" 
            class="profile-avatar-large"
          >
        <?php else: ?>
          <div class="profile-avatar-placeholder">
            <?= strtoupper(substr($student['full_name'] ?? 'S', 0, 1)) ?>
          </div>
        <?php endif; ?>

        <div class="profile-meta">
          <h2><?= htmlspecialchars($student['full_name']) ?></h2>
          <p><strong>Roll Number:</strong> <?= htmlspecialchars($student['roll_number']) ?></p>
          <p><span class="badge badge-green"><?= htmlspecialchars($student['course']) ?></span></p>
        </div>
      </div>

      <div class="profile-body">
        <div class="detail-grid">
          <div class="detail-item">
            <div class="detail-label">Institutional Email</div>
            <div class="detail-value"><?= htmlspecialchars($student['email']) ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Enrolled Academic Program</div>
            <div class="detail-value"><?= htmlspecialchars($student['course']) ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Registration Timestamp</div>
            <div class="detail-value"><?= date('F d, Y - h:i A', strtotime($student['created_at'])) ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Last Modified Timestamp</div>
            <div class="detail-value"><?= date('F d, Y - h:i A', strtotime($student['updated_at'])) ?></div>
          </div>
        </div>

        <!-- File Metadata Section -->
        <h4 style="margin: 24px 0 12px 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
          Stored File Metadata in MySQL
        </h4>

        <div class="detail-grid">
          <!-- Profile Image Metadata -->
          <div class="detail-item">
            <div class="detail-label">Profile Image Asset</div>
            <?php if (!empty($student['profile_image'])): ?>
              <div class="detail-value">
                <p><strong>Stored Filename:</strong> <code><?= htmlspecialchars($student['profile_image']) ?></code></p>
                <p><strong>MIME Type:</strong> <?= htmlspecialchars($student['profile_image_type'] ?? 'image/jpeg') ?></p>
                <p><strong>File Size:</strong> <?= round(($student['profile_image_size'] ?? 0) / 1024, 1) ?> KB</p>
              </div>
            <?php else: ?>
              <div class="detail-value" style="color: #64748b;">No profile image uploaded.</div>
            <?php endif; ?>
          </div>

          <!-- Document Metadata -->
          <div class="detail-item">
            <div class="detail-label">Verification Document Asset</div>
            <?php if (!empty($student['document'])): ?>
              <div class="detail-value">
                <p><strong>Stored Filename:</strong> <code><?= htmlspecialchars($student['document']) ?></code></p>
                <p><strong>MIME Type:</strong> <?= htmlspecialchars($student['document_type'] ?? 'application/pdf') ?></p>
                <p><strong>File Size:</strong> <?= round(($student['document_size'] ?? 0) / 1024, 1) ?> KB</p>
                <div style="margin-top: 10px;">
                  <a href="uploads/<?= htmlspecialchars($student['document']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                    📥 Download & View Document
                  </a>
                </div>
              </div>
            <?php else: ?>
              <div class="detail-value" style="color: #64748b;">No verification document attached.</div>
            <?php endif; ?>
          </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
          <a href="index.php" class="btn btn-secondary">&larr; Return to Dashboard</a>
          <div style="display: flex; gap: 10px;">
            <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn btn-primary">Edit Profile</a>
            <a 
              href="delete_student.php?id=<?= $student['id'] ?>" 
              class="btn btn-danger"
              onclick="return confirm('Are you sure you want to permanently delete this student record?');"
            >
              Delete Student
            </a>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
