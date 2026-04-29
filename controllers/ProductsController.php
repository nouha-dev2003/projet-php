<?php
// controllers/ProductsController.php

require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Uploader.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/ProductDao.php';
require_once __DIR__ . '/../models/daos/CategoryDao.php';
require_once __DIR__ . '/../models/entities/Product.php';
require_once __DIR__ . '/../models/entities/Category.php'; // <-- ADD THIS LINE

class ProductsController
{

    private ProductDao $productDao;
    private CategoryDao $categoryDao;

    public function __construct()
    {
        $pdo = \Config\Database::getInstance();  // Keep the backslash
        $this->productDao = new ProductDao($pdo);
        $this->categoryDao = new CategoryDao($pdo);
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
            die('CSRF token validation failed.');
        }
    }

    private function setFlash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'][$type] = $message;
    }

    private function getFlash(): array
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    public function index(): void
    {
        Auth::requireLogin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;
        $search = trim($_GET['search'] ?? '');

        $total = $this->productDao->count($search ?: null);
        $products = $this->productDao->findPaginated($limit, $offset, $search ?: null);
        $totalPages = ceil($total / $limit);

        $categories = [];
        foreach ($this->categoryDao->findAll() as $cat) {
            $categories[$cat->getId()] = $cat->getName();
        }

        $flash = $this->getFlash();
        $csrfToken = $this->generateCsrfToken();
        require __DIR__ . '/../views/product/index.php';
    }

    public function show(int $id): void
    {
        Auth::requireLogin();
        $product = $this->productDao->find($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found.";
            return;
        }
        $categoryName = null;
        if ($product->getCategoryId()) {
            $cat = $this->categoryDao->find($product->getCategoryId());
            $categoryName = $cat ? $cat->getName() : null;
        }
        $flash = $this->getFlash();
        require __DIR__ . '/../views/product/show.php';
    }

    public function create(): void
    {
        Auth::requireLogin();
        $csrfToken = $this->generateCsrfToken();
        $categories = $this->categoryDao->findAll();
        $flash = $this->getFlash();
        require __DIR__ . '/../views/product/create.php';
    }

    public function store(): void
    {
        Auth::requireLogin();
        $this->verifyCsrfToken();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

        $errors = [];
        if (strlen($name) < 2) $errors[] = "Name must be at least 2 characters.";
        if ($price <= 0) $errors[] = "Price must be greater than 0.";
        if (strlen($description) > 1000) $errors[] = "Description cannot exceed 1000 characters.";

        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $targetDir = __DIR__ . '/../public/uploads/products/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
                $uploader = new Uploader();
                $newFilename = $uploader->upload($_FILES['image'], $targetDir, 2 * 1024 * 1024, ['jpg', 'jpeg', 'png', 'gif']);
                $imagePath = 'uploads/products/' . $newFilename;
            } catch (Exception $e) {
                $errors[] = "Image upload failed: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = implode('<br>', $errors);
            header('Location: index.php?route=products/create');
            exit;
        }

        $product = new Product([
            'name'        => $name,
            'description' => $description,
            'price'       => $price,
            'category_id' => $categoryId,
            'image_path'  => $imagePath
        ]);

        $newId = $this->productDao->save($product);
        if ($newId) {
            $this->setFlash('success', "Product created successfully.");
            header('Location: index.php?route=products/show/' . $newId);
        } else {
            $this->setFlash('error', "Failed to create product.");
            header('Location: index.php?route=products/create');
        }
        exit;
    }

    public function edit(int $id): void
    {
        Auth::requireLogin();
        $product = $this->productDao->find($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found.";
            return;
        }
        $csrfToken = $this->generateCsrfToken();
        $categories = $this->categoryDao->findAll();
        $flash = $this->getFlash();
        require __DIR__ . '/../views/product/edit.php';
    }

    public function update(int $id): void
    {
        Auth::requireLogin();
        $this->verifyCsrfToken();

        $product = $this->productDao->find($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found.";
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

        $errors = [];
        if (strlen($name) < 2) $errors[] = "Name must be at least 2 characters.";
        if ($price <= 0) $errors[] = "Price must be greater than 0.";
        if (strlen($description) > 1000) $errors[] = "Description cannot exceed 1000 characters.";

        $imagePath = $product->getImagePath();
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                if (!empty($imagePath) && file_exists(__DIR__ . '/../public/' . $imagePath)) {
                    unlink(__DIR__ . '/../public/' . $imagePath);
                }
                $targetDir = __DIR__ . '/../public/uploads/products/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
                $uploader = new Uploader();
                $newFilename = $uploader->upload($_FILES['image'], $targetDir, 2 * 1024 * 1024, ['jpg', 'jpeg', 'png', 'gif']);
                $imagePath = 'uploads/products/' . $newFilename;
            } catch (Exception $e) {
                $errors[] = "Image upload failed: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = implode('<br>', $errors);
            header('Location: index.php?route=products/edit/' . $id);
            exit;
        }

        $product->setName($name)
                ->setDescription($description)
                ->setPrice($price)
                ->setCategoryId($categoryId)
                ->setImagePath($imagePath);

        $result = $this->productDao->save($product);
        if ($result) {
            $this->setFlash('success', "Product updated successfully.");
            header('Location: index.php?route=products/show/' . $id);
        } else {
            $this->setFlash('error', "Failed to update product.");
            header('Location: index.php?route=products/edit/' . $id);
        }
        exit;
    }

    public function delete(int $id): void
    {
        Auth::requireLogin();
        $this->verifyCsrfToken();

        $product = $this->productDao->find($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found.";
            return;
        }

        $imagePath = $product->getImagePath();
        if (!empty($imagePath) && file_exists(__DIR__ . '/../public/' . $imagePath)) {
            unlink(__DIR__ . '/../public/' . $imagePath);
        }

        $deleted = $this->productDao->delete($id);
        if ($deleted) {
            $this->setFlash('success', "Product deleted successfully.");
        } else {
            $this->setFlash('error', "Failed to delete product.");
        }
        header('Location: index.php?route=products/index');
        exit;
    }
}