<?php
/**
 * ==============================================================================
 * Web Technology Assignment II - Question 2: Student CRUD Dashboard
 * File: Question2/index.php
 * 
 * Displays student records with search, filter, file links, and CRUD action buttons.
 * Uses PDO prepared statements for security against SQL injection.
 * ==============================================================================
 */

session_start();
require_once __DIR__ . '/db.php';

// Flash message extraction
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Filter & Search Parameters
$search = trim($_GET['search'] ?? '');
$courseFilter = trim($_GET['course'] ?? '');

// Build Prepared Query Dynamically
$sql = "SELECT * FROM students WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (full_name LIKE :search OR roll_number LIKE :search OR email LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($courseFilter !== '') {
    $sql .= " AND course = :course";
    $params[':course'] = $courseFilter;
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();

    // Fetch distinct courses for the filter dropdown
    $coursesStmt = $pdo->query("SELECT DISTINCT course FROM students ORDER BY course ASC");
    $allCourses = $coursesStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Database query error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Records Management | Question 2 (PHP & MySQL)</title>
  <link rel="stylesheet" href="style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <!-- Navbar / Top Header -->
    <header class="navbar">
      <div class="nav-brand">
        <h2>Student Management System</h2>
        <span class="subtitle">Question 2 &bull; PHP PDO Prepared Statements & MySQL CRUD</span>
      </div>
      <div class="nav-actions">
        <a href="add_student.php" class="btn btn-primary">+ Add New Student</a>
      </div>
    </header>

    <!-- Flash Alert Notifications -->
    <?php if ($flashSuccess): ?>
      <div class="alert alert-success">
        <span><strong>Success:</strong> <?= htmlspecialchars($flashSuccess) ?></span>
      </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
      <div class="alert alert-danger">
        <span><strong>Error:</strong> <?= htmlspecialchars($flashError) ?></span>
      </div>
    <?php endif; ?>

    <!-- Search & Filter Controls -->
    <form method="GET" action="index.php" class="filters-bar">
      <input 
        type="text" 
        name="search" 
        class="search-input" 
        placeholder="Search by name, roll number, or email..." 
        value="<?= htmlspecialchars($search) ?>"
      >
      <select name="course" class="filter-select">
        <option value="">All Courses</option>
        <?php foreach ($allCourses as $c): ?>
          <option value="<?= htmlspecialchars($c) ?>" <?= ($courseFilter === $c) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary">Filter</button>
      <?php if ($search !== '' || $courseFilter !== ''): ?>
        <a href="index.php" class="btn btn-secondary">Clear</a>
      <?php endif; ?>
    </form>

    <!-- Student Table Card -->
    <main class="table-card">
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Avatar</th>
              <th>Roll Number</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Course</th>
              <th>Document</th>
              <th>Created</th>
              <th style="text-align: right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($students) > 0): ?>
              <?php foreach ($students as $stu): ?>
                <tr>
                  <!-- Profile Image / Avatar -->
                  <td>
                    <?php if (!empty($stu['profile_image']) && file_exists(__DIR__ . '/uploads/' . $stu['profile_image'])): ?>
                      <img 
                        src="uploads/<?= htmlspecialchars($stu['profile_image']) ?>" 
                        alt="Profile of <?= htmlspecialchars($stu['full_name']) ?>" 
                        class="avatar-thumb"
                      >
                    <?php else: ?>
                      <div class="avatar-placeholder">
                        <?= strtoupper(substr($stu['full_name'] ?? 'S', 0, 1)) ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <!-- Student Info -->
                  <td><strong><?= htmlspecialchars($stu['roll_number']) ?></strong></td>
                  <td><?= htmlspecialchars($stu['full_name']) ?></td>
                  <td><?= htmlspecialchars($stu['email']) ?></td>
                  <td><span class="badge badge-blue"><?= htmlspecialchars($stu['course']) ?></span></td>

                  <!-- Document Attachment -->
                  <td>
                    <?php if (!empty($stu['document']) && file_exists(__DIR__ . '/uploads/' . $stu['document'])): ?>
                      <a 
                        href="uploads/<?= htmlspecialchars($stu['document']) ?>" 
                        target="_blank" 
                        class="badge badge-green"
                        style="text-decoration: none;"
                        title="Download Document (<?= round(($stu['document_size'] ?? 0) / 1024, 1) ?> KB)"
                      >
                        📄 Download
                      </a>
                    <?php else: ?>
                      <span class="badge badge-gray">None</span>
                    <?php endif; ?>
                  </td>

                  <!-- Timestamp -->
                  <td><small class="subtitle"><?= date('M d, Y', strtotime($stu['created_at'])) ?></small></td>

                  <!-- CRUD Action Buttons -->
                  <td style="text-align: right; white-space: nowrap;">
                    <a href="view_student.php?id=<?= $stu['id'] ?>" class="btn btn-secondary btn-sm" title="View Profile">👁 View</a>
                    <a href="edit_student.php?id=<?= $stu['id'] ?>" class="btn btn-secondary btn-sm" title="Edit Student">✏ Edit</a>
                    <a 
                      href="delete_student.php?id=<?= $stu['id'] ?>" 
                      class="btn btn-danger btn-sm" 
                      title="Delete Student" 
                      onclick="return confirm('Are you sure you want to permanently delete student <?= htmlspecialchars(addslashes($stu['full_name'])) ?>?');"
                    >
                      🗑 Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="empty-state">
                  <p>No student records found matching your query.</p>
                  <p><a href="add_student.php" class="btn btn-primary btn-sm" style="margin-top: 10px;">+ Register First Student</a></p>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
