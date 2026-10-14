<?php
/**
 * ==============================================================================
 * Web Technology Assignment II - Question 2: File Upload Utility
 * File: Question2/upload.php
 * 
 * Handles secure file validation, MIME verification, size restrictions,
 * unique filename generation, and safe disk storage.
 * ==============================================================================
 */

// Upload Directories and Constraints
define('UPLOAD_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);

// Maximum File Sizes
define('MAX_IMAGE_SIZE_BYTES', 2 * 1024 * 1024);   // 2 MB max for profile images
define('MAX_DOC_SIZE_BYTES', 5 * 1024 * 1024);     // 5 MB max for student documents

// Whitelisted MIME Types and Extensions
const ALLOWED_IMAGE_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

const ALLOWED_DOC_TYPES = [
    'application/pdf'  => 'pdf',
    'application/msword' => 'doc',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
];

/**
 * Validates and stores an uploaded file.
 * 
 * @param array  $fileArray  The $_FILES['input_name'] array.
 * @param string $category   'image' or 'document'.
 * @return array             Result array ['success' => bool, 'filename' => string, 'type' => string, 'size' => int, 'error' => string]
 */
function handleFileUpload(array $fileArray, string $category = 'image'): array {
    // 1. Check if a file was provided and check native PHP upload error
    if (!isset($fileArray['error']) || is_array($fileArray['error'])) {
        return ['success' => false, 'error' => 'Invalid file parameter provided.'];
    }

    if ($fileArray['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'No file was uploaded.', 'no_file' => true];
    }

    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds upload_max_filesize in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds MAX_FILE_SIZE specified in the form.',
            UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
        ];
        $msg = $uploadErrors[$fileArray['error']] ?? 'Unknown file upload error.';
        return ['success' => false, 'error' => $msg];
    }

    // 2. Validate Size Limit
    $maxSize = ($category === 'image') ? MAX_IMAGE_SIZE_BYTES : MAX_DOC_SIZE_BYTES;
    $fileSize = (int)$fileArray['size'];

    if ($fileSize > $maxSize) {
        $limitMb = round($maxSize / (1024 * 1024), 1);
        return [
            'success' => false,
            'error'   => sprintf('File size (%s MB) exceeds the allowable %s MB limit.', round($fileSize / (1024 * 1024), 2), $limitMb)
        ];
    }

    // 3. Strict Server-Side MIME-type Verification via finfo
    $tmpPath = $fileArray['tmp_name'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $detectedMime = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    $allowedMap = ($category === 'image') ? ALLOWED_IMAGE_TYPES : ALLOWED_DOC_TYPES;

    if (!array_key_exists($detectedMime, $allowedMap)) {
        $allowedList = implode(', ', array_values($allowedMap));
        return [
            'success' => false,
            'error'   => sprintf('Invalid file format (%s). Permitted extensions: %s.', htmlspecialchars($detectedMime), strtoupper($allowedList))
        ];
    }

    // 4. Validate extension matching detected MIME
    $origName = $fileArray['name'];
    $origExt = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

    // Normalize jpeg -> jpg
    if ($origExt === 'jpeg') {
        $origExt = 'jpg';
    }

    // 5. Generate Cryptographically Secure Unique Filename
    // Format: prefix_timestamp_random.extension
    $prefix = ($category === 'image') ? 'profile_' : 'doc_';
    $uniqueName = $prefix . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $origExt;

    // 6. Ensure destination directory exists and is writable
    if (!is_dir(UPLOAD_DIR)) {
        if (!mkdir(UPLOAD_DIR, 0755, true)) {
            return ['success' => false, 'error' => 'Failed to create server upload storage directory.'];
        }
    }

    $targetPath = UPLOAD_DIR . $uniqueName;

    // 7. Move uploaded file from temp location
    if (!move_uploaded_file($tmpPath, $targetPath)) {
        return ['success' => false, 'error' => 'Could not move uploaded file to permanent destination.'];
    }

    return [
        'success'   => true,
        'filename'  => $uniqueName,
        'file_type' => $detectedMime,
        'file_size' => $fileSize
    ];
}

/**
 * Safely removes a file from the uploads directory.
 * Prevents Directory Traversal attacks using basename().
 * 
 * @param string|null $filename
 * @return bool
 */
function deleteUploadedFile(?string $filename): bool {
    if (empty($filename)) {
        return false;
    }

    // Sanitize filename to strip directory traversal tokens (../)
    $cleanFilename = basename($filename);
    $filePath = UPLOAD_DIR . $cleanFilename;

    if (file_exists($filePath) && is_file($filePath)) {
        return unlink($filePath);
    }

    return false;
}
