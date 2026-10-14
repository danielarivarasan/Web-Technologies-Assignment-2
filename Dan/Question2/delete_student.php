<?php
/**
 * ==============================================================================
 * Web Technology Assignment II - Question 2: Delete Student Record
 * File: Question2/delete_student.php
 * 
 * Safely removes a student from MySQL via PDO prepared statements and deletes
 * associated uploaded files (profile image and document) from server storage.
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
    $_SESSION['flash_error'] = 'Invalid student ID specified for deletion.';
    header('Location: index.php');
    exit;
}

try {
    // 1. Fetch student to get filenames of uploaded assets
    $stmt = $pdo->prepare("SELECT full_name, roll_number, profile_image, document FROM students WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $student = $stmt->fetch();

    if (!$student) {
        $_SESSION['flash_error'] = 'Student record does not exist or has already been deleted.';
        header('Location: index.php');
        exit;
    }

    // 2. Delete student record from MySQL
    $delStmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
    $delStmt->execute([':id' => $id]);

    // 3. Remove physical files from server uploads directory
    if (!empty($student['profile_image'])) {
        deleteUploadedFile($student['profile_image']);
    }

    if (!empty($student['document'])) {
        deleteUploadedFile($student['document']);
    }

    $_SESSION['flash_success'] = "Student '{$student['full_name']}' ({$student['roll_number']}) was successfully deleted along with associated files.";
} catch (PDOException $e) {
    $_SESSION['flash_error'] = "Database deletion failed: " . $e->getMessage();
}

header('Location: index.php');
exit;
