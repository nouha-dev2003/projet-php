<?php
// utils/Uploader.php

class Uploader
{
    /**
     * Upload a file securely.
     *
     * @param array $file The $_FILES['field'] array.
     * @param string $targetDir Absolute path to target directory.
     * @param int $maxSize Max file size in bytes (default 2MB).
     * @param array $allowedTypes Allowed file extensions (e.g., ['jpg', 'png', 'pdf']).
     * @return string The generated unique filename.
     * @throws Exception On validation failure or move error.
     */
    public static function upload(array $file, string $targetDir, int $maxSize = 2097152, array $allowedTypes = ['jpg', 'png', 'pdf']): string
    {
        // 1. Check if file was uploaded without errors
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new \RuntimeException('Invalid file parameters.');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('File upload error: ' . self::getUploadErrorMessage($file['error']));
        }

        // 2. Validate size
        if ($file['size'] > $maxSize) {
            throw new \RuntimeException('File too large. Max size: ' . ($maxSize / 1024 / 1024) . 'MB');
        }

        // 3. Validate extension and MIME type
        $originalName = $file['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            throw new \RuntimeException('Invalid file type. Allowed: ' . implode(', ', $allowedTypes));
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
        ];
        $expectedMime = $mimeMap[$extension] ?? '';
        if ($mimeType !== $expectedMime) {
            throw new \RuntimeException('MIME type mismatch. Expected ' . $expectedMime . ', got ' . $mimeType);
        }

        // 4. Generate unique name
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        $newFilename = $timestamp . '_' . $random . '.' . $extension;

        // 5. Create target directory if it doesn't exist
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new \RuntimeException('Failed to create target directory.');
            }
        }

        // 6. Move file
        $targetPath = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newFilename;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Failed to move uploaded file.');
        }

        return $newFilename;
    }

    private static function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive.';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk.';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload.';
            default:
                return 'Unknown upload error.';
        }
    }
}