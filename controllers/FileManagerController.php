<?php
// controllers/FileManagerController.php

require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Uploader.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/FileDao.php';
require_once __DIR__ . '/../models/entities/File.php';

use Config\Database;

class FileManagerController
{
    private FileDao $fileDao;

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->fileDao = new FileDao($pdo);
    }

    private function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    private function verifyCsrfToken(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            http_response_code(403);
            die('CSRF token validation failed. Unauthorized manipulation rejected.');
        }
    }

    public function index()
    {
        Auth::requireLogin();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo "Access denied. Admin only.";
            return;
        }

        $files = $this->fileDao->findAll();
        
        $flash = [];
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        require __DIR__ . '/../views/file_manager/index.php';
    }

    public function create()
    {
        Auth::requireLogin();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo "Access denied.";
            return;
        }

        $csrfToken = $this->generateCsrfToken();
        require __DIR__ . '/../views/file_manager/upload.php';
    }

    public function store()
    {
        Auth::requireLogin();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo "Access denied.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=filemanager/index');
            exit;
        }
        
        $this->verifyCsrfToken();

        if (session_status() === PHP_SESSION_NONE) session_start();

        try {
            if (!isset($_FILES['file'])) {
                throw new \RuntimeException("No file was uploaded by the browser.");
            }

            $targetDir = __DIR__ . '/../public/uploads/files/';
            $newFilename = Uploader::upload($_FILES['file'], $targetDir);

            // Establish the explicit original name securely
            $originalName = trim($_POST['name'] ?? '');
            if (empty($originalName)) {
                $originalName = $_FILES['file']['name'];
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['file']['tmp_name']);
            finfo_close($finfo);

            $fileObj = new File([
                'original_name' => $originalName,
                'stored_name'   => 'uploads/files/' . $newFilename,
                'mime_type'     => $mimeType,
                'size'          => $_FILES['file']['size'],
                'user_id'       => Auth::getUserId(),
            ]);

            $id = $this->fileDao->save($fileObj);

            if ($id) {
                $_SESSION['flash']['success'] = "File uploaded securely to volume and db.";
            } else {
                $_SESSION['flash']['error'] = "System Fault: File saved to disk but failed Database transaction.";
            }

        } catch (\RuntimeException $e) {
            $_SESSION['flash']['error'] = "Upload Constraints Failed: " . $e->getMessage();
        }

        header('Location: index.php?route=filemanager/index');
        exit;
    }

    public function delete($id)
    {
        Auth::requireLogin();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo "Access denied.";
            return;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();
        $id = (int) $id;

        $fileObj = $this->fileDao->find($id);

        if (!$fileObj) {
            $_SESSION['flash']['error'] = "System Warning: File pointer could not be found.";
            header('Location: index.php?route=filemanager/index');
            exit;
        }

        // Purge memory space on drive
        $diskPath = __DIR__ . '/../public/' . ltrim($fileObj->getStoredName(), '/');
        if (file_exists($diskPath)) {
            unlink($diskPath);
        }

        // Purge logic from DAO
        if ($this->fileDao->delete($id)) {
            $_SESSION['flash']['success'] = "File purged safely from local disk and tables.";
        } else {
            $_SESSION['flash']['error'] = "Database fault executing logical sweep.";
        }

        header('Location: index.php?route=filemanager/index');
        exit;
    }
}