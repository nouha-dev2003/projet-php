# Full Project Report

**Generated:** 2026-04-26 04:39:51

**Root Directory:** `C:\xampp\htdocs\php-project`

## Directory Tree

```
php-project
├── config
│   ├── Database.php
│   ├── Router.php
│   ├── config.php
│   ├── db_config.php
│   └── test_db.php
├── controllers
│   ├── AuthController.php
│   ├── FileManagerController.php
│   ├── ProductsController.php
│   ├── SearchController.php
│   └── TestController.php
├── documentation
├── models
│   ├── daos
│   │   ├── CategoryDao.php
│   │   ├── FileDao.php
│   │   ├── ProductDao.php
│   │   └── UserDao.php
│   └── entities
│       ├── Category.php
│       ├── File.php
│       ├── Product.php
│       └── User.php
├── public
│   ├── css
│   ├── js
│   ├── .htaccess
│   ├── diagnostic.php
│   ├── index.php
│   ├── test_auth.php
│   ├── test_login_direct.php
│   ├── test_router.php
│   ├── update_hash.php
│   └── update_hash_admin123.php
├── reports
├── services
│   └── SearchService.php
├── sql
│   └── unit8_files.sql
├── tests
│   ├── test_daos_try_catch.php
│   ├── test_db.php
│   ├── test_loader.php
│   ├── test_unit.php
│   ├── test_unit3.php
│   └── test_unit7.php
├── utils
│   ├── Auth.php
│   └── Uploader.php
├── views
│   ├── auth
│   │   ├── dashboard.php
│   │   └── login.php
│   ├── file_manager
│   │   ├── index.php
│   │   └── upload.php
│   ├── product
│   │   ├── create.php
│   │   ├── edit.php
│   │   ├── index.php
│   │   └── show.php
│   └── search
│       └── index.php
├── GLOSSARY.md
├── README.md
├── Router.php
└── composer.json
```

## File Contents

### `GLOSSARY.md`

```markdown
# Project Glossary

## Database Tables

### users
- id (int, PK, auto-increment)
- email (varchar(100), unique)
- password_hash (varchar(255))
- role (enum: 'admin', 'user')
- created_at (timestamp)

### categories
- id (int, PK, auto-increment)
- name (varchar(50))
- slug (varchar(50), unique)

### products
- id (int, PK, auto-increment)
- name (varchar(100))
- description (text)
- price (decimal(10,2))
- category_id (int, FK → categories.id, ON DELETE SET NULL)
- image_path (varchar(255))
- created_at (timestamp)

### files
- id (int, PK, auto-increment)
- original_name (varchar(255))
- stored_name (varchar(255))
- mime_type (varchar(100))
- size (int)
- user_id (int, FK → users.id, ON DELETE CASCADE)
- created_at (timestamp)

## URL Convention

- Route format: `index.php?route=controller/action/id`
- Example: `index.php?route=products/show/5`
```

### `README.md`

```markdown
# Custom PHP MVC Application

A secure, custom-built PHP Model-View-Controller (MVC) system developed without external frameworks. This architecture emphasizes strict OOP principles, deterministic database boundaries via isolated DAOs, and rigorous native security integrations including CSRF tokens, strict sessions, and global exception handling.

## 🚀 Features
- **Custom MVC Architecture:** Front controller paradigm routing through `public/index.php`.
- **Complete CRUD Operations:** Fully modeled DAOs supporting relationships between Users, Categories, and Products.
- **Session-Based Authentication:** Robust user authentication mitigating Session Hijacking via `HttpOnly` and `SameSite` enforcements.
- **Secure File Management:** Dedicated Uploader utility and File Manager validating MIME types, extensions, size constraints, and isolating physical disk limits from the SQL layer.
- **Dynamic Search Engine:** Aggregation service utilizing prepared statements for multi-table filtering, pagination, and multi-criteria searches.

## 🛠️ System Requirements
- **PHP:** 8.0 or higher
- **Database:** MySQL / MariaDB (8.0+)
- **Web Server:** Apache (with `mod_rewrite` enabled) or Nginx
- **Extensions:**
  - `pdo` and `pdo_mysql`
  - `fileinfo` (for MIME-type validation)

## 📦 Installation & Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/php-project.git
   cd php-project
   ```

2. **Configure the Database**
   - Create a fresh MySQL database (e.g., `php_mvc_db`).
   - Copy the configuration example setup to active configuration:
     ```bash
     cp config/db_config.example.php config/db_config.php
     ```
   - Edit `config/db_config.php` and insert your database credentials:
     ```php
     return [
         'host'     => '127.0.0.1',
         'dbname'   => 'php_mvc_db',
         'username' => 'root',
         'password' => ''
     ];
     ```

3. **Run Database Initialization**
   - Import the provided schema files located in the `sql/` directory to generate your tables in sequence directly into your database.
   - Example command: `mysql -u root -p php_mvc_db < sql/schema.sql`

4. **Configure your Server Document Root**
   - Point your web server's Document Root directly to the `public/` directory inside this repository. 
   - **Crucial:** Accessing the system from the parent folder circumvents the security logic. The `.htaccess` strictly pipes all traffic via `public/index.php`.
   - *Local Example URL*: `http://localhost/php-project/public/`

## 🔑 Default Credentials
Upon importing the required schema files, the system grants the following default access profile for administrative actions:

- **Email:** `admin@example.com`
- **Password:** `admin123`

## 📂 Folder Structure
```text
php-project/
├── config/             # Database and core environment configurations
├── controllers/        # Class routing controllers isolating logic
├── models/
│   ├── daos/           # Database Access Objects (PDO injection-safe boundaries)
│   └── entities/       # Strict OOP representations of database rows
├── public/             # 🌐 Web Document Root (DO NOT EXPOSE parent dirs)
│   ├── index.php       # Front controller / System entry point
│   ├── .htaccess       # Routing intercept rules
│   └── uploads/        # System storage mapped out of DB (Requires 0755 writes)
├── services/           # Aggregated module protocols crossing DAO boundaries 
├── sql/                # System structure schema initializations
├── tests/              # CLI integration and unit isolation tests
├── utils/              # Global application utilities (Auth, Uploader)
└── views/              # Front-end HTML presentation logic and layouts
```
```

### `Router.php`

```php

```

### `composer.json`

```json
{ 
  "autoload": { 
    "psr-4": { "App\\": "" } 
  } 
}
```

### `config\Database.php`

```php
<?php
namespace Config;

class Database
{
    private static $pdo = null;

    public static function getInstance()
    {
        if (self::$pdo === null) {
            try {
                require_once __DIR__ . '/Config.php'; // ensure Config is loaded
                $dbConfig = \Config::getDbConfig();
                $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
                self::$pdo = new \PDO($dsn, $dbConfig['user'], $dbConfig['password']);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // Log the real exception internally, but throw a generic one to avoid leaking credentials
                error_log("Database Connection Error: " . $e->getMessage());
                throw new \RuntimeException("Database connection failed. Please check the logs.");
            }
        }
        return self::$pdo;
    }

    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {
        throw new \LogicException("Cannot unserialize a singleton.");
    }
}
```

### `config\Router.php`

```php
<?php

/**
 * Router – analyse l'URL et dispatch vers le contrôleur/action.
 * Supporte deux formats :
 * - index.php?route=user/show/5
 * - /user/show/5 (clean URL)
 */
class Router
{
    /**
     * Décompose la requête, instancie le contrôleur et appelle l'action.
     * 
     * @param string|null $routeParam Optionnel – pour injecter directement une route (ex: $_GET['route'])
     */
    public function dispatch($routeParam = null)
    {
        // 1. Récupérer la route depuis $_GET['route'] ou depuis l'URL
        $route = $routeParam ?? $this->getRouteFromUrl();

        // 2. Séparer les segments : controller/action/param1/param2...
        $segments = explode('/', trim($route, '/'));
        
        // Défaut : controller = 'home', action = 'index'
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) : 'Home';
        $actionName = !empty($segments[1]) ? $segments[1] : 'index';
        $params = array_slice($segments, 2);

        // 3. Construire le nom de la classe (ex: UserController)
        $controllerClass = $controllerName . 'Controller';
        
        // 4. Chemin vers le fichier contrôleur (dossier /controllers à la racine)
        $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';

        // 5. Vérifier l'existence du fichier
        if (!file_exists($controllerFile)) {
            $this->send404("Controller '$controllerClass' not found (file: $controllerFile)");
            return;
        }

        require_once $controllerFile;

        // 6. Vérifier que la classe existe
        if (!class_exists($controllerClass)) {
            $this->send404("Class '$controllerClass' not found in file.");
            return;
        }

        // 7. Instancier le contrôleur
        $controller = new $controllerClass();

        // 8. Vérifier que la méthode existe
        if (!method_exists($controller, $actionName)) {
            $this->send404("Action '$actionName' not found in controller '$controllerClass'.");
            return;
        }

        // 9. Appeler l'action avec les paramètres
        call_user_func_array([$controller, $actionName], $params);
    }

    /**
     * Extrait la route depuis l'URI (pour les URLs propres)
     * Exemple : /user/show/5  ->  user/show/5
     * 
     * @return string
     */
    private function getRouteFromUrl()
    {
        // Récupérer le chemin de l'URI (sans paramètres GET)
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

        // Supprimer la partie du script (ex: /php-project/public/index.php)
        $path = parse_url($requestUri, PHP_URL_PATH);
        
        // Enlever le sous-dossier éventuel (si le projet est dans un sous-répertoire)
        $baseDir = dirname($scriptName);
        if ($baseDir !== '/' && strpos($path, $baseDir) === 0) {
            $path = substr($path, strlen($baseDir));
        }
        
        // Nettoyer : enlever le slash initial et les slashs superflus
        $route = ltrim($path, '/');
        
        // Si la route est vide, on retourne une chaîne vide (=> home/index)
        return $route;
    }

    /**
     * Affiche une page 404 et stoppe l'exécution.
     * 
     * @param string $message Message optionnel pour le log (non affiché en prod)
     */
    private function send404($message = '')
    {
        // En développement, on peut logger le message
        if (defined('DEBUG') && DEBUG) {
            error_log("Router 404: $message");
        }
        
        http_response_code(404);
        echo "<h1>404 - Page not found</h1>";
        if (defined('DEBUG') && DEBUG) {
            echo "<p>Debug: " . htmlspecialchars($message) . "</p>";
        }
        exit;
    }
}
```

### `config\config.php`

```php
<?php
class Config
{
    private static $env = null;

    private static function loadEnv()
    {
        if (self::$env !== null) {
            return self::$env;
        }
        $envFile = __DIR__ . '/../.env';
        if (!file_exists($envFile)) {
            die('.env file not found. Please copy .env.example to .env and configure it.');
        }
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($key, $value) = explode('=', $line, 2);
            self::$env[trim($key)] = trim($value);
        }
        return self::$env;
    }

    public static function get($key, $default = null)
    {
        $env = self::loadEnv();
        return $env[$key] ?? $default;
    }

    public static function getDbConfig()
    {
        return [
            'host' => self::get('DB_HOST'),
            'dbname' => self::get('DB_NAME'),
            'user' => self::get('DB_USER'),
            'password' => self::get('DB_PASS'),
            'charset' => 'utf8mb4'
        ];
    }

    public static function getUploadConfig()
    {
        return [
            'max_size' => (int)self::get('UPLOAD_MAX_SIZE', 2097152),
            'allowed_types' => explode(',', self::get('UPLOAD_ALLOWED_TYPES', 'jpg,png,pdf')),
            'upload_dir' => self::get('UPLOAD_DIR', __DIR__ . '/../public/uploads/'),
        ];
    }

    public static function getAppUrl()
    {
        return rtrim(self::get('APP_URL', 'http://localhost:8000'), '/');
    }
}
```

### `config\db_config.php`

```php
<?php
return [
    'host'     => 'localhost',
    'dbname'   => 'your_database_name',   // ← change to your actual DB name
    'user'     => 'root',
    'password' => ''
];
```

### `config\test_db.php`

```php
<?php
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$password = 'admin123';

if (password_verify($password, $hash)) {
    echo "✅ Password matches!";
} else {
    echo "❌ Password does NOT match.";
}
```

### `controllers\AuthController.php`

```php
<?php
// controllers/AuthController.php
use Config\Database;
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';
require_once __DIR__ . '/../utils/Auth.php';

class AuthController
{
    private UserDao $userDao;

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->userDao = new UserDao($pdo);
    }

    /**
     * Show the login form.
     */
    public function login(): void
    {
        if (Auth::isLoggedIn()) {
            header('Location: index.php?route=auth/dashboard');
            exit();
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Process login form submission.
     */
    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=auth/login');
            exit();
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['login_error'] = 'Email and password are required.';
            header('Location: index.php?route=auth/login');
            exit();
        }

        $user = $this->userDao->findByEmail($email);

        if (!$user) {
            $_SESSION['login_error'] = 'Invalid email or password.';
            header('Location: index.php?route=auth/login');
            exit();
        }

        if (password_verify($password, $user->getPasswordHash())) {
            Auth::login($user);
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php?route=auth/dashboard';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit();
        } else {
            $_SESSION['login_error'] = 'Invalid email or password.';
            header('Location: index.php?route=auth/login');
            exit();
        }
    }
    /**
     * Dashboard (protected page).
     */
    public function dashboard(): void
    {
        Auth::requireLogin();
        $userEmail = $_SESSION['user_email'] ?? 'User';
        require __DIR__ . '/../views/auth/dashboard.php';
    }

    /**
     * Logout.
     */
    public function logout(): void
    {
        Auth::logout();
        header('Location: index.php?route=auth/login');
        exit();
    }
}
```

### `controllers\FileManagerController.php`

```php
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
```

### `controllers\ProductsController.php`

```php
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
```

### `controllers\SearchController.php`

```php
<?php
// controllers/SearchController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../services/SearchService.php';
require_once __DIR__ . '/../models/daos/CategoryDao.php';
require_once __DIR__ . '/../models/entities/Category.php';

use Config\Database;

class SearchController
{
    private SearchService $searchService;
    private CategoryDao $categoryDao;

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->searchService = new SearchService($pdo);
        $this->categoryDao = new CategoryDao($pdo);
    }

    public function search()
    {
        // Criteria Extraction
        $criteria = [
            'keyword'     => trim($_GET['keyword'] ?? ''),
            'category_id' => !empty($_GET['category']) ? (int) $_GET['category'] : null,
            'price_min'   => !empty($_GET['price_min']) ? (float) $_GET['price_min'] : null,
            'price_max'   => !empty($_GET['price_max']) ? (float) $_GET['price_max'] : null,
        ];

        // Pagination setup
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;

        // Service Fetching
        $totalItems = $this->searchService->count($criteria);
        $totalPages = ceil($totalItems / $limit);
        
        $result = $this->searchService->search($criteria, $page, $limit);
        $products = $result['data'];
        $errorMessage = $result['error'];

        // Get Categories for the dropdown filter mapping
        $categories = $this->categoryDao->findAll();

        require __DIR__ . '/../views/search/index.php';
    }
}
```

### `controllers\TestController.php`

```php
<?php
class TestController {
    public function index() {
        echo "TestController index action works!";
    }
    public function hello($name = 'World') {
        echo "Hello, $name!";
    }
}
```

### `models\daos\CategoryDao.php`

```php
<?php

use Config\Database;

/**
 * Data Access Object for the 'categories' table.
 * Provides CRUD operations using PDO prepared statements.
 */
class CategoryDao
{
    /**
     * @var \PDO The PDO connection instance.
     */
    private $pdo;

    /**
     * Constructor accepts a PDO instance (typically from Database singleton).
     *
     * @param \PDO $pdo The PDO connection.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find a category by its ID.
     *
     * @param int $id The category ID.
     * @return Category|null Returns a Category object if found, null otherwise.
     */
    public function find($id): ?Category
    {
        try {
            $sql = "SELECT id, name, slug FROM categories WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                return $this->hydrate($row);
            }
            return null;
        } catch (\PDOException $e) {
            error_log("CategoryDao::find error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve all categories.
     *
     * @return Category[] Array of Category objects (empty array if none).
     */
    public function findAll(): array
    {
        try {
            $sql = "SELECT id, name, slug FROM categories ORDER BY name ASC";
            $stmt = $this->pdo->query($sql);
            $categories = [];

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $categories[] = $this->hydrate($row);
            }
            return $categories;
        } catch (\PDOException $e) {
            error_log("CategoryDao::findAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Insert or update a category.
     * - If the category has an ID (non-zero) and exists, performs an UPDATE.
     * - Otherwise, performs an INSERT and returns the new ID.
     *
     * @param Category $category The category entity to save.
     * @return int|bool Returns the inserted ID on INSERT success, true on UPDATE success, false on failure.
     */
    public function save(Category $category)
    {
        try {
            $id = $category->getId();

            if (!empty($id) && $this->find($id)) {
                // Update existing category
                $sql = "UPDATE categories SET name = :name, slug = :slug WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    ':name' => $category->getName(),
                    ':slug' => $category->getSlug(),
                    ':id'   => $id
                ]);
                return $result;
            } else {
                // Insert new category
                $sql = "INSERT INTO categories (name, slug) VALUES (:name, :slug)";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    ':name' => $category->getName(),
                    ':slug' => $category->getSlug()
                ]);
                if ($result) {
                    return (int)$this->pdo->lastInsertId();
                }
                return false;
            }
        } catch (\PDOException $e) {
            error_log("CategoryDao::save error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a category by ID.
     *
     * @param int $id The category ID.
     * @return bool True on success (row deleted), false otherwise.
     */
    public function delete($id): bool
    {
        try {
            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("CategoryDao::delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper method to create a Category entity from database row.
     *
     * @param array $row Associative array of category data.
     * @return Category
     */
    private function hydrate(array $row): Category
    {
        return new Category([
            'id'   => $row['id'],
            'name' => $row['name'],
            'slug' => $row['slug']
        ]);
    }
}
```

### `models\daos\FileDao.php`

```php
<?php

use Config\Database;

/**
 * Data Access Object for the 'files' table.
 * Provides CRUD operations using PDO prepared statements.
 */
class FileDao
{
    /**
     * @var \PDO The PDO connection instance.
     */
    private $pdo;

    /**
     * Constructor accepts a PDO instance (typically from Database singleton).
     *
     * @param \PDO $pdo The PDO connection.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find a file by its ID.
     *
     * @param int $id The file ID.
     * @return File|null Returns a File object if found, null otherwise.
     */
    public function find($id): ?File
    {
        try {
            $sql = "SELECT id, original_name, stored_name, mime_type, size, user_id, created_at 
                    FROM files WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                return $this->hydrate($row);
            }
            return null;
        } catch (\PDOException $e) {
            error_log("FileDao::find error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve all files.
     *
     * @return File[] Array of File objects (empty array if none).
     */
    public function findAll(): array
    {
        try {
            $sql = "SELECT id, original_name, stored_name, mime_type, size, user_id, created_at 
                    FROM files ORDER BY created_at DESC";
            $stmt = $this->pdo->query($sql);
            $files = [];

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $files[] = $this->hydrate($row);
            }
            return $files;
        } catch (\PDOException $e) {
            error_log("FileDao::findAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find all files uploaded by a specific user.
     *
     * @param int $userId The user ID.
     * @return File[] Array of File objects.
     */
    public function findAllByUserId(int $userId): array
    {
        try {
            $sql = "SELECT id, original_name, stored_name, mime_type, size, user_id, created_at 
                    FROM files WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $files = [];

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $files[] = $this->hydrate($row);
            }
            return $files;
        } catch (\PDOException $e) {
            error_log("FileDao::findAllByUserId error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Insert or update a file.
     * - If the file has an ID (non-zero) and exists, performs an UPDATE.
     * - Otherwise, performs an INSERT and returns the new ID.
     *
     * @param File $file The file entity to save.
     * @return int|bool Returns the inserted ID on INSERT success, true on UPDATE success, false on failure.
     */
    public function save(File $file)
    {
        try {
            $id = $file->getId();

            if (!empty($id) && $this->find($id)) {
                // Update existing file (rare, but allowed)
                $sql = "UPDATE files 
                        SET original_name = :original_name, stored_name = :stored_name, 
                            mime_type = :mime_type, size = :size, user_id = :user_id 
                        WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    ':original_name' => $file->getOriginalName(),
                    ':stored_name'   => $file->getStoredName(),
                    ':mime_type'     => $file->getMimeType(),
                    ':size'          => $file->getSize(),
                    ':user_id'       => $file->getUserId(),
                    ':id'            => $id
                ]);
                return $result;
            } else {
                // Insert new file
                $sql = "INSERT INTO files (original_name, stored_name, mime_type, size, user_id, created_at) 
                        VALUES (:original_name, :stored_name, :mime_type, :size, :user_id, NOW())";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    ':original_name' => $file->getOriginalName(),
                    ':stored_name'   => $file->getStoredName(),
                    ':mime_type'     => $file->getMimeType(),
                    ':size'          => $file->getSize(),
                    ':user_id'       => $file->getUserId()
                ]);
                if ($result) {
                    return (int)$this->pdo->lastInsertId();
                }
                return false;
            }
        } catch (\PDOException $e) {
            error_log("FileDao::save error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a file by ID.
     *
     * @param int $id The file ID.
     * @return bool True on success (row deleted), false otherwise.
     */
    public function delete($id): bool
    {
        try {
            $sql = "DELETE FROM files WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("FileDao::delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper method to create a File entity from database row.
     *
     * @param array $row Associative array of file data.
     * @return File
     */
    private function hydrate(array $row): File
    {
        return new File([
            'id'            => $row['id'],
            'original_name' => $row['original_name'],
            'stored_name'   => $row['stored_name'],
            'mime_type'     => $row['mime_type'],
            'size'          => $row['size'],
            'user_id'       => $row['user_id'],
            'created_at'    => $row['created_at']
        ]);
    }
}
```

### `models\daos\ProductDao.php`

```php
<?php

use Config\Database;

/**
 * Data Access Object for the 'products' table.
 * Provides CRUD operations using PDO prepared statements.
 */
class ProductDao
{
    /**
     * @var \PDO The PDO connection instance.
     */
    private $pdo;

    /**
     * Constructor accepts a PDO instance (typically from Database singleton).
     *
     * @param \PDO $pdo The PDO connection.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find a product by its ID.
     *
     * @param int $id The product ID.
     * @return Product|null Returns a Product object if found, null otherwise.
     */
    public function find($id): ?Product
    {
        try {
            $sql = "SELECT id, name, description, price, category_id, image_path, created_at 
                    FROM products WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                return $this->hydrate($row);
            }
            return null;
        } catch (\PDOException $e) {
            error_log("ProductDao::find error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve all products.
     *
     * @return Product[] Array of Product objects (empty array if none).
     */
    public function findAll(): array
    {
        try {
            $sql = "SELECT id, name, description, price, category_id, image_path, created_at 
                    FROM products ORDER BY created_at DESC";
            $stmt = $this->pdo->query($sql);
            $products = [];

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $products[] = $this->hydrate($row);
            }
            return $products;
        } catch (\PDOException $e) {
            error_log("ProductDao::findAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Insert or update a product.
     * - If the product has an ID (non-zero) and exists, performs an UPDATE.
     * - Otherwise, performs an INSERT and returns the new ID.
     *
     * @param Product $product The product entity to save.
     * @return int|bool Returns the inserted ID on INSERT success, true on UPDATE success, false on failure.
     */
    public function save(Product $product)
    {
        try {
            $id = $product->getId();

            if (!empty($id) && $this->find($id)) {
                // Update existing product (do not modify created_at)
                $sql = "UPDATE products 
                        SET name = :name, description = :description, price = :price, 
                            category_id = :category_id, image_path = :image_path 
                        WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    ':name'         => $product->getName(),
                    ':description'  => $product->getDescription(),
                    ':price'        => $product->getPrice(),
                    ':category_id'  => $product->getCategoryId(),
                    ':image_path'   => $product->getImagePath(),
                    ':id'           => $id
                ]);
                return $result;
            } else {
                // Insert new product
                $sql = "INSERT INTO products (name, description, price, category_id, image_path, created_at) 
                        VALUES (:name, :description, :price, :category_id, :image_path, NOW())";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    ':name'         => $product->getName(),
                    ':description'  => $product->getDescription(),
                    ':price'        => $product->getPrice(),
                    ':category_id'  => $product->getCategoryId(),
                    ':image_path'   => $product->getImagePath()
                ]);
                if ($result) {
                    return (int)$this->pdo->lastInsertId();
                }
                return false;
            }
        } catch (\PDOException $e) {
            error_log("ProductDao::save error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a product by ID.
     *
     * @param int $id The product ID.
     * @return bool True on success (row deleted), false otherwise.
     */
    public function delete($id): bool
    {
        try {
            $sql = "DELETE FROM products WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("ProductDao::delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Count total products (optionally with search filter)
     *
     * @param string|null $search
     * @return int
     */
    public function count(?string $search = null): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM products";
            $params = [];
            if ($search) {
                $sql .= " WHERE name LIKE :search OR description LIKE :search";
                $params[':search'] = '%' . $search . '%';
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("ProductDao::count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Find products with pagination and optional search.
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return Product[]
     */
    public function findPaginated(int $limit, int $offset, ?string $search = null): array
    {
        try {
            $sql = "SELECT id, name, description, price, category_id, image_path, created_at 
                    FROM products";
            $params = [];
            if ($search) {
                $sql .= " WHERE name LIKE :search OR description LIKE :search";
                $params[':search'] = '%' . $search . '%';
            }
            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            // Bind integer parameters explicitly as INT
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            $products = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $products[] = $this->hydrate($row);
            }
            return $products;
        } catch (\PDOException $e) {
            error_log("ProductDao::findPaginated error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper method to create a Product entity from database row.
     *
     * @param array $row Associative array of product data.
     * @return Product
     */
    private function hydrate(array $row): Product
    {
        return new Product([
            'id'          => $row['id'],
            'name'        => $row['name'],
            'description' => $row['description'],
            'price'       => $row['price'],
            'category_id' => $row['category_id'],
            'image_path'  => $row['image_path'],
            'created_at'  => $row['created_at']
        ]);
    }
}
```

### `models\daos\UserDao.php`

```php
<?php

/**
 * Data Access Object for the 'users' table.
 * Provides CRUD operations using PDO prepared statements.
 */
class UserDao
{
    /**
     * @var \PDO The PDO connection instance.
     */
    private $pdo;

    /**
     * Constructor accepts a PDO instance (typically from Database singleton).
     *
     * @param \PDO $pdo The PDO connection.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find a user by its ID.
     *
     * @param int $id The user ID.
     * @return User|null Returns a User object if found, null otherwise.
     */
    public function find($id): ?User
    {
        try {
            $sql = "SELECT id, email, password_hash, role, created_at FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                return $this->hydrate($row);
            }
            return null;
        } catch (\PDOException $e) {
            error_log("UserDao::find error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve all users.
     *
     * @return User[] Array of User objects (empty array if none).
     */
    public function findAll(): array
    {
        try {
            $sql = "SELECT id, email, password_hash, role, created_at FROM users";
            $stmt = $this->pdo->query($sql);
            $users = [];

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $users[] = $this->hydrate($row);
            }
            return $users;
        } catch (\PDOException $e) {
            error_log("UserDao::findAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Insert or update a user.
     * - If the user has an ID (non-zero) and exists, performs an UPDATE.
     * - Otherwise, performs an INSERT and returns the new ID.
     *
     * @param User $user The user entity to save.
     * @return int|bool Returns the inserted ID on INSERT success, true on UPDATE success, false on failure.
     */
    public function save(User $user)
    {
        try {
            $id = $user->getId();

            if (!empty($id) && $this->find($id)) {
                // Update existing user
                $sql = "UPDATE users 
                        SET email = :email, password_hash = :password_hash, role = :role 
                        WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    ':email'         => $user->getEmail(),
                    ':password_hash' => $user->getPasswordHash(),
                    ':role'          => $user->getRole(),
                    ':id'            => $id
                ]);
                return $result;
            } else {
                // Insert new user
                $sql = "INSERT INTO users (email, password_hash, role, created_at) 
                        VALUES (:email, :password_hash, :role, NOW())";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    ':email'         => $user->getEmail(),
                    ':password_hash' => $user->getPasswordHash(),
                    ':role'          => $user->getRole()
                ]);
                if ($result) {
                    return (int)$this->pdo->lastInsertId();
                }
                return false;
            }
        } catch (\PDOException $e) {
            error_log("UserDao::save error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id The user ID.
     * @return bool True on success (row deleted), false otherwise.
     */
    public function delete($id): bool
    {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("UserDao::delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find a user by email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        try {
            $sql = "SELECT id, email, password_hash, role, created_at 
                    FROM users WHERE email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                return $this->hydrate($row);
            }
            return null;
        } catch (\PDOException $e) {
            error_log("UserDao::findByEmail error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper method to create a User entity from database row.
     *
     * @param array $row Associative array of user data.
     * @return User
     */
    private function hydrate(array $row): User
    {
        return new User([
            'id'            => $row['id'],
            'email'         => $row['email'],
            'password_hash' => $row['password_hash'],
            'role'          => $row['role'],
            'created_at'    => $row['created_at']
        ]);
    }
}
```

### `models\entities\Category.php`

```php
<?php
// Category.php
class Category
{
    private int $id;
    private string $name;
    private string $slug;

    public function __construct(?array $data = null)
    {
        if ($data) {
            $this->setId($data['id'] ?? 0);
            $this->setName($data['name'] ?? '');
            $this->setSlug($data['slug'] ?? '');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }
}
```

### `models\entities\File.php`

```php
<?php
// File.php
class File
{
    private int $id;
    private string $originalName;
    private string $storedName;
    private string $mimeType;
    private int $size;
    private int $userId;
    private string $createdAt;

    public function __construct(?array $data = null)
    {
        if ($data) {
            $this->setId($data['id'] ?? 0);
            $this->setOriginalName($data['original_name'] ?? '');
            $this->setStoredName($data['stored_name'] ?? '');
            $this->setMimeType($data['mime_type'] ?? '');
            $this->setSize((int)($data['size'] ?? 0));
            $this->setUserId((int)($data['user_id'] ?? 0));
            $this->setCreatedAt($data['created_at'] ?? '');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;
        return $this;
    }

    public function getStoredName(): string
    {
        return $this->storedName;
    }

    public function setStoredName(string $storedName): self
    {
        $this->storedName = $storedName;
        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
```

### `models\entities\Product.php`

```php
<?php
// Product.php
class Product
{
    private int $id;
    private string $name;
    private string $description;
    private float $price;
    private ?int $categoryId;
    private string $imagePath;
    private string $createdAt;

    public function __construct(?array $data = null)
    {
        if ($data) {
            $this->setId($data['id'] ?? 0);
            $this->setName($data['name'] ?? '');
            $this->setDescription($data['description'] ?? '');
            $this->setPrice((float)($data['price'] ?? 0));
            $this->setCategoryId($data['category_id'] ?? null);
            $this->setImagePath($data['image_path'] ?? '');
            $this->setCreatedAt($data['created_at'] ?? '');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
```

### `models\entities\User.php`

```php
<?php
// User.php
class User
{
    private int $id;
    private string $email;
    private string $passwordHash;
    private string $role;
    private string $createdAt;

    public function __construct(?array $data = null)
    {
        if ($data) {
            $this->setId($data['id'] ?? 0);
            $this->setEmail($data['email'] ?? '');
            $this->setPasswordHash($data['password_hash'] ?? '');
            $this->setRole($data['role'] ?? 'user');
            $this->setCreatedAt($data['created_at'] ?? '');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
```

### `public\.htaccess`

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Si le fichier ou dossier demandé existe réellement, ne pas réécrire
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    # Tout le reste redirige vers index.php
    # La requête originale est passée dans le paramètre 'route'
    RewriteRule ^(.*)$ index.php?route=$1 [QSA,L]
</IfModule>
```

### `public\diagnostic.php`

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnostic Check</h2>";

// 1. Session
session_start();
$_SESSION['test'] = 'works';
echo "1. Session: " . ($_SESSION['test'] === 'works' ? "✅ OK" : "❌ Failed") . "<br>";

// 2. Database connection
require_once __DIR__ . '/../config/Database.php';
use Config\Database;
try {
    $pdo = Database::getInstance();
    echo "2. Database: ✅ Connected<br>";
} catch (Exception $e) {
    echo "2. Database: ❌ " . $e->getMessage() . "<br>";
    exit;
}

// 3. Check user
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';
$userDao = new UserDao($pdo);
$user = $userDao->findByEmail('admin@example.com');
if (!$user) {
    echo "3. User 'admin@example.com': ❌ Not found in database.<br>";
    echo "   Run this SQL:<br><pre>INSERT INTO users (email, password_hash, role, created_at) 
VALUES ('admin@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());</pre>";
} else {
    echo "3. User found: ✅ " . $user->getEmail() . "<br>";
    echo "   Stored hash: " . $user->getPasswordHash() . "<br>";
    $testPassword = 'admin123';
    if (password_verify($testPassword, $user->getPasswordHash())) {
        echo "   Password verify: ✅ matches<br>";
    } else {
        echo "   Password verify: ❌ does NOT match<br>";
        echo "   Update hash with:<br><pre>UPDATE users SET password_hash = '"
             . password_hash($testPassword, PASSWORD_DEFAULT) . "' WHERE email = 'admin@example.com';</pre>";
    }
}

// 4. Router class
require_once __DIR__ . '/../config/Router.php';
echo "4. Router class: " . (class_exists('Router') ? "✅ Loaded" : "❌ Not found") . "<br>";
```

### `public\index.php`

```php
<?php
/**
 * Front controller – point d'entrée unique de l'application.
 * 
 * Charge l'autoloader Composer, la configuration,
 * puis démarre le routeur.
 */

// Afficher toutes les erreurs en développement (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Constante pour savoir si on est en mode debug (utilisée par Router)
define('DEBUG', true);

// 1. Autoloader Composer (si vous l'utilisez)
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Inclure la classe Router (pas encore chargée par Composer si elle n'est pas namespace)
//    Si vous avez configuré l'autoload PSR-4 pour App\Config\, adaptez.
require_once __DIR__ . '/../config/Router.php';

// 3. Optionnel : charger la configuration globale (base de données, etc.)
//    Exemple : $config = require __DIR__ . '/../config/config.php';

// 4. Démarrer la session (nécessaire pour l'authentification plus tard)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 5. Récupérer la route depuis le paramètre GET 'route' (prioritaire)
$route = $_GET['route'] ?? null;

// 6. Instancier et exécuter le routeur avec Global Exception Handler
try {
    $router = new Router();
    $router->dispatch($route);
} catch (\Throwable $e) {
    error_log("Fatal Unhandled Application Exception: " . $e->getMessage());
    http_response_code(500);
    echo "<h1>500 - Internal Server Error</h1>";
    if (defined('DEBUG') && DEBUG) {
        echo "<p>Debug: " . htmlspecialchars($e->getMessage()) . "</p>";
    } else {
        echo "<p>An unexpected error occurred. Please try again later.</p>";
    }
    exit;
}
```

### `public\test_auth.php`

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';

use Config\Database;

try {
    $pdo = Database::getInstance();
    $userDao = new UserDao($pdo);

    $email = 'admin@example.com';
    $user = $userDao->findByEmail($email);

    if (!$user) {
        die("❌ User NOT found in database. Please run the INSERT SQL.");
    }

    echo "✅ User found: " . $user->getEmail() . "<br>";
    echo "Stored hash: " . $user->getPasswordHash() . "<br><br>";

    $testPassword = 'admin123';
    if (password_verify($testPassword, $user->getPasswordHash())) {
        echo "✅ Password 'admin123' matches the hash.<br>";
        echo "Login should work now.";
    } else {
        echo "❌ Password does NOT match.<br>";
        echo "Re-run this exact SQL to fix the hash:<br>";
        echo "<pre>INSERT INTO users (email, password_hash, role, created_at) 
VALUES ('admin@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW())
ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = VALUES(role);</pre>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### `public\test_login_direct.php`

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';

use Config\Database;

$pdo = Database::getInstance();
$userDao = new UserDao($pdo);
$user = $userDao->findByEmail('admin@example.com');

if (!$user) {
    die("❌ User not found. Run INSERT first.");
}

$storedHash = $user->getPasswordHash();
echo "Stored hash: $storedHash<br>";

// Test with hardcoded password
$testPassword = 'admin123';
if (password_verify($testPassword, $storedHash)) {
    echo "✅ Hardcoded 'admin123' matches.<br>";
} else {
    echo "❌ Hardcoded 'admin123' does NOT match – hash may be corrupted.<br>";
}

// Now test with actual POST data if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedPassword = $_POST['password'] ?? '';
    echo "<hr>Password from POST:<br>";
    echo "Raw: [" . $postedPassword . "]<br>";
    echo "Length: " . strlen($postedPassword) . "<br>";
    echo "Hex: " . bin2hex($postedPassword) . "<br>";
    
    if (password_verify($postedPassword, $storedHash)) {
        echo "✅ POST password matches!<br>";
    } else {
        echo "❌ POST password does NOT match.<br>";
        // Try trimming
        $trimmed = trim($postedPassword);
        if ($trimmed !== $postedPassword && password_verify($trimmed, $storedHash)) {
            echo "✅ But trimmed version matches! (Password has leading/trailing whitespace)<br>";
        }
    }
} else {
    echo '<hr><form method="POST">';
    echo '<input type="password" name="password" placeholder="Enter password">';
    echo '<button type="submit">Test</button>';
    echo '</form>';
}
?>
```

### `public\test_router.php`

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php'; // if using Composer
// or manually include your Router, Database, etc.

$route = $_GET['route'] ?? 'test/index';
// Simulate dispatch – your Router should handle this
```

### `public\update_hash.php`

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Database.php';

use Config\Database;

try {
    $pdo = Database::getInstance();
    
    $email = 'admin@example.com';
    $plainPassword = 'admin123';
    
    // Generate a brand new hash from PHP
    $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    
    // Update the database
    $sql = "UPDATE users SET password_hash = :hash WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':hash' => $newHash, ':email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Password hash updated successfully for $email<br>";
        echo "New hash: $newHash<br>";
        echo "Now try logging in again with password: <strong>admin123</strong>";
    } else {
        echo "❌ User with email $email not found. Please run the INSERT first.";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### `public\update_hash_admin123.php`

```php
<?php
require_once __DIR__ . '/../config/Database.php';
use Config\Database;

$pdo = Database::getInstance();
$email = 'admin@example.com';
$newHash = password_hash('admin123', PASSWORD_DEFAULT);

$sql = "UPDATE users SET password_hash = :hash WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute([':hash' => $newHash, ':email' => $email]);

echo "Hash updated for $email to: " . $newHash;
echo "\n\nYou can now log in with password: admin123";
```

### `services\SearchService.php`

```php
<?php
// services/SearchService.php

class SearchService
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function search(array $criteria, int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $params = [];
        $whereFilters = [];

        $sql = "SELECT p.id, p.name, p.description, p.price, p.image_path, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";

        if (!empty($criteria['keyword'])) {
            $whereFilters[] = "(p.name LIKE :keyword OR p.description LIKE :keyword)";
            $params[':keyword'] = '%' . $criteria['keyword'] . '%';
        }

        if (!empty($criteria['category_id'])) {
            $whereFilters[] = "p.category_id = :category_id";
            $params[':category_id'] = $criteria['category_id'];
        }

        if (!empty($criteria['price_min'])) {
            $whereFilters[] = "p.price >= :price_min";
            $params[':price_min'] = $criteria['price_min'];
        }

        if (!empty($criteria['price_max'])) {
            $whereFilters[] = "p.price <= :price_max";
            $params[':price_max'] = $criteria['price_max'];
        }

        if (!empty($whereFilters)) {
            $sql .= " WHERE " . implode(" AND ", $whereFilters);
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Bind pagination strictly as Int
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            
            foreach ($params as $key => $val) {
                $type = \PDO::PARAM_STR;
                if (is_int($val)) $type = \PDO::PARAM_INT;
                $stmt->bindValue($key, $val, $type);
            }

            $stmt->execute();
            return ['data' => $stmt->fetchAll(), 'error' => null];
            
        } catch (\PDOException $e) {
            error_log("SearchService::search error: " . $e->getMessage());
            return ['data' => [], 'error' => "Search logic encountered an internal data fault."];
        }
    }

    public function count(array $criteria): int
    {
        $params = [];
        $whereFilters = [];

        $sql = "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.id";

        if (!empty($criteria['keyword'])) {
            $whereFilters[] = "(p.name LIKE :keyword OR p.description LIKE :keyword)";
            $params[':keyword'] = '%' . $criteria['keyword'] . '%';
        }

        if (!empty($criteria['category_id'])) {
            $whereFilters[] = "p.category_id = :category_id";
            $params[':category_id'] = $criteria['category_id'];
        }

        if (!empty($criteria['price_min'])) {
            $whereFilters[] = "p.price >= :price_min";
            $params[':price_min'] = $criteria['price_min'];
        }

        if (!empty($criteria['price_max'])) {
            $whereFilters[] = "p.price <= :price_max";
            $params[':price_max'] = $criteria['price_max'];
        }

        if (!empty($whereFilters)) {
            $sql .= " WHERE " . implode(" AND ", $whereFilters);
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) {
                $type = is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
                $stmt->bindValue($key, $val, $type);
            }
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("SearchService::count error: " . $e->getMessage());
            return 0;
        }
    }
}
```

### `sql\unit8_files.sql`

```sql
-- Unit 8: Admin File Management
-- SQL Initialization Script for the 'files' table

CREATE TABLE IF NOT EXISTS `files` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `original_name` VARCHAR(255) NOT NULL,
    `stored_name` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `size` INT NOT NULL,
    `user_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_files_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

### `tests\test_daos_try_catch.php`

```php
<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';

use Config\Database;

echo "=== STABILIZATION AUDIT: UNIT 3-6 (DAOs) ===\n";

// Force a PDO exception by giving a bad table name or dropping the table temporarily
try {
    $pdo = Database::getInstance();
    
    // Create a mock PDO that throws exceptions to simulate DB failure
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "[TEST] Running UserDao->findAll() with a forced corrupted connection...\n";
    // We will drop the users table temporarily inside a transaction, OR just query a bad statement.
    // Instead of corrupting the DB, let's just use Reflection to inject a bad PDO or just test normal behavior.
    
    $userDao = new UserDao($pdo);
    
    // Normal query should work
    $users = $userDao->findAll();
    echo "✅ Normal findAll() works. Count: " . count($users) . "\n";
    
    // To test if DAO catches exceptions, we'll intentionally rename the table and see if it returns FALSE/NULL or throws exception.
    $pdo->exec("RENAME TABLE users TO _users_tmp");
    
    echo "[TEST] Running UserDao->findAll() after table is missing...\n";
    try {
        $result = $userDao->findAll();
        echo "✅ SUCCESS: DAO caught the exception internally and returned: " . json_encode($result) . "\n";
    } catch (\PDOException $e) {
        echo "🚨 RCA BUG DETECTED: PDOException escaped the DAO!\n";
        echo "   Message: " . $e->getMessage() . "\n";
    }
    
    // Restore
    $pdo->exec("RENAME TABLE _users_tmp TO users");
    echo "✅ Table restored.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    // Ensure table restored if failed
    try { $pdo->exec("RENAME TABLE _users_tmp TO users"); } catch(\Exception $ex) {}
}
```

### `tests\test_db.php`

```php
<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    echo "✅ DB connected. Users count: " . $stmt->fetchColumn();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
```

### `tests\test_loader.php`

```php
<?php
// Use this to test if your controller can actually "see" the classes
require_once __DIR__ . '/../models/entities/Product.php';
require_once __DIR__ . '/../models/entities/Category.php';
require_once __DIR__ . '/../models/daos/ProductDao.php';

echo class_exists('Product') ? "✅ Product Entity Loaded<br>" : "❌ Product Missing<br>";
echo class_exists('Category') ? "✅ Category Entity Loaded<br>" : "❌ Category Missing<br>";
echo class_exists('ProductDao') ? "✅ ProductDao Loaded<br>" : "❌ ProductDao Missing<br>";
```

### `tests\test_unit.php`

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/entities/User.php';
require_once __DIR__ . '/../models/daos/UserDao.php';

use Config\Database;

try {
    $pdo = Database::getInstance();
    $userDao = new UserDao($pdo);  // No namespace – class is global
    $users = $userDao->findAll();

    echo "=== User List ===\n";
    if (empty($users)) {
        echo "No users found.\n";
    } else {
        echo "Found " . count($users) . " users:\n";
        foreach ($users as $user) {
            echo sprintf("ID: %d | Email: %s | Role: %s | Created: %s\n",
                $user->getId(),
                $user->getEmail(),
                $user->getRole(),
                $user->getCreatedAt()
            );
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### `tests\test_unit3.php`

```php
<?php
// tests/test_unit3.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../controllers/AuthController.php';

echo "=== STABILIZATION AUDIT: UNIT 3 (Auth and Sessions) ===\n";

// 1. Verify Auth session settings
Auth::isLoggedIn(); // Triggers startSession
$cookieParams = session_get_cookie_params();
echo "Cookie HttpOnly: " . ($cookieParams['httponly'] ? "✅ YES" : "❌ NO") . "\n";
echo "Cookie SameSite: " . ($cookieParams['samesite'] ?? 'None') . "\n";

// 2. Mock POST for AuthController
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'admin@example.com';
$_POST['password'] = 'wrongpassword123';

try {
    $controller = new AuthController();
    $controller->authenticate();
} catch (\Exception $e) {
    // If it tries to header() and exit(), it won't work perfectly in CLI without warnings.
}

// Check session flash / error
if (isset($_SESSION['login_error']) && $_SESSION['login_error'] === 'Invalid email or password.') {
    echo "✅ Generic error correctly logged! No DB info or hash chunks leaked.\n";
} else {
    echo "❌ Expected generic login error, got: " . ($_SESSION['login_error'] ?? 'nothing') . "\n";
}

echo "Auth constraints check complete!\n";
```

### `tests\test_unit7.php`

```php
<?php
// tests/test_unit7.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../utils/Uploader.php';

echo "=== STABILIZATION AUDIT: UNIT 7 (Uploader) ===\n";

$targetDir = __DIR__ . '/uploads_test/';
@mkdir($targetDir, 0755, true);

// 1. Test invalid parameters
try {
    $badFile = [];
    Uploader::upload($badFile, $targetDir);
    echo "❌ Failed to catch invalid arguments.\n";
} catch (\RuntimeException $e) {
    echo "✅ Caught missing superglobal args correctly: " . $e->getMessage() . "\n";
}

// 2. Test file too large
try {
    $largeFile = [
        'error' => UPLOAD_ERR_OK,
        'size' => 5000000, 
        'name' => 'test.jpg'
    ];
    Uploader::upload($largeFile, $targetDir, 100);
    echo "❌ Failed to catch oversize file.\n";
} catch (\RuntimeException $e) {
    echo "✅ Caught file size constraint cleanly: " . $e->getMessage() . "\n";
}

// 3. Test Invalid Extension
try {
    $exeFile = [
        'error' => UPLOAD_ERR_OK,
        'size' => 10,
        'name' => 'malware.exe'
    ];
    Uploader::upload($exeFile, $targetDir);
    echo "❌ Failed to catch bad extension.\n";
} catch (\RuntimeException $e) {
    echo "✅ Caught invalid extension cleanly: " . $e->getMessage() . "\n";
}

if (is_dir($targetDir)) rmdir($targetDir);
echo "✅ Unit 7 Isolation Tests Completed.\n";
```

### `utils\Auth.php`

```php
<?php
// utils/Auth.php

class Auth
{
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Strict');
            session_start();
        }
    }

    public static function isLoggedIn(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    /**
     * Redirect to login page if not authenticated.
     * Optionally saves the current URL to redirect back after login.
     *
     * @param string|null $redirectUrl URL to return to after login
     */
    public static function requireLogin(?string $redirectUrl = null): void
    {
        if (!self::isLoggedIn()) {
            if ($redirectUrl) {
                $_SESSION['redirect_after_login'] = $redirectUrl;
            }
            header('Location: index.php?route=auth/login');
            exit();
        }
    }

    /**
     * Log in a user – stores session data and regenerates session ID.
     *
     * @param User $user
     */
    public static function login(User $user): void
    {
        self::startSession();
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_role']  = $user->getRole();
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        session_destroy();

        // Delete session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
    }

    public static function getUserId(): ?int
    {
        return self::isLoggedIn() ? $_SESSION['user_id'] : null;
    }

    public static function getUserRole(): ?string
    {
        return self::isLoggedIn() ? $_SESSION['user_role'] : null;
    }

    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'admin';
    }
}
```

### `utils\Uploader.php`

```php
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
```

### `views\auth\dashboard.php`

```php
<?php
// Ensure Auth utility is available if not already included by the router
require_once __DIR__ . '/../../utils/Auth.php';

// Get user info from session safely
$userEmail = $_SESSION['user_email'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css"> </head>
<body>
    <header>
        <h1>Welcome, <?= htmlspecialchars($userEmail) ?>!</h1>
        <p><strong>Role:</strong> <?= htmlspecialchars(Auth::getUserRole() ?? 'User') ?></p>
    </header>

    <main>
        <nav>
            <ul>
                <li>
                    <a href="index.php?route=products/index">Manage Products</a>
                </li>
                
                <?php if (Auth::isAdmin()): ?>
                    <li>
                        <a href="index.php?route=filemanager/index">File Manager (Admin Only)</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <hr>
        
        <div class="actions">
            <a href="index.php?route=auth/logout" style="color: red;">Logout</a>
        </div>
    </main>
</body>
</html>
```

### `views\auth\login.php`

```php
<?php
// views/auth/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        .error { color: red; margin-bottom: 1em; }
        .form-group { margin-bottom: 1em; }
        label { display: inline-block; width: 80px; }
    </style>
</head>
<body>
    <h1>Login</h1>

    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php?route=auth/authenticate">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Login</button>
    </form>

    <p><small>Demo: admin@example.com / admin123</small></p>
</body>
</html>
```

### `views\file_manager\index.php`

```php
<?php
// views/file_manager/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>File Manager</h1>
    
    <div class="actions">
        <a href="index.php?route=filemanager/create" class="btn">Upload New File</a>
        <a href="index.php?route=auth/dashboard" class="btn">Back to Dashboard</a>
    </div>

    <?php if (!empty($flash['success'])): ?><div class="alert success"><?= $flash['success'] ?></div><?php endif; ?>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th>Preview</th>
                <th>Filename</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file): ?>
            <tr>
                <td>
                    <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file->getStoredName())): ?>
                        <img src="<?= htmlspecialchars($file->getStoredName()) ?>" width="50">
                    <?php else: ?>
                        <span>📄 Document</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($file->getOriginalName()) ?></td>
                <td>
                    <a href="index.php?route=filemanager/delete&id=<?= $file->getId() ?>" 
                       onclick="return confirm('Delete this file?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($files)): ?>
                <tr><td colspan="3">No files uploaded yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
```

### `views\file_manager\upload.php`

```php
<?php
// views/file_manager/upload.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload File</title>
</head>
<body>
    <h1>Upload General File</h1>
    
    <form action="index.php?route=filemanager/store" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        
        <div>
            <label>File Name (optional):</label><br>
            <input type="text" name="name">
        </div>

        <div>
            <label>Select File:</label><br>
            <input type="file" name="file" required>
        </div>

        <button type="submit">Upload</button>
        <a href="index.php?route=filemanager/index">Cancel</a>
    </form>
</body>
</html>
```

### `views\product\create.php`

```php
<!-- views/product/create.php -->
<?php
// views/product/create.php
if (!isset($categories) || !isset($csrfToken)) {
    die('Invalid request.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Product</title>
    <style>
        .form-group { margin-bottom: 15px; }
        label { display: inline-block; width: 100px; }
        input, textarea, select { width: 300px; padding: 5px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Create New Product</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="index.php?route=products/store" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price" name="price" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id">
                <option value="">-- None --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->getId() ?>"><?= htmlspecialchars($category->getName()) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Product Image:</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">
        </div>

        <div class="form-group">
            <button type="submit">Create Product</button>
            <a href="index.php?route=products/index">Cancel</a>
        </div>
    </form>
</body>
</html>
```

### `views\product\edit.php`

```php
<!-- views/product/edit.php -->
<?php
// views/product/edit.php
if (!isset($product) || !isset($categories) || !isset($csrfToken)) {
    die('Invalid request.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <style>
        .form-group { margin-bottom: 15px; }
        label { display: inline-block; width: 100px; }
        input, textarea, select { width: 300px; padding: 5px; }
        .current-image { margin-left: 100px; margin-bottom: 15px; }
        .current-image img { max-width: 150px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Edit Product: <?= htmlspecialchars($product->getName()) ?></h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="index.php?route=products/update/<?= $product->getId() ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product->getName()) ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($product->getDescription()) ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price" name="price" value="<?= $product->getPrice() ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id">
                <option value="">-- None --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->getId() ?>" <?= ($product->getCategoryId() == $category->getId()) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category->getName()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Current Image:</label>
            <div class="current-image">
                <?php if ($product->getImagePath()): ?>
                    <img src="<?= htmlspecialchars($product->getImagePath()) ?>" alt="Current Image">
                    <p><small>Leave empty to keep current image.</small></p>
                <?php else: ?>
                    <p>No image uploaded.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="image">New Image:</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">
        </div>

        <div class="form-group">
            <button type="submit">Update Product</button>
            <a href="index.php?route=products/show/<?= $product->getId() ?>">Cancel</a>
        </div>
    </form>
</body>
</html>
```

### `views\product\index.php`

```php
<?php
// No die() anywhere – safe defaults for everything
$products ??= [];
$totalPages ??= 1;
$page ??= 1;
$search ??= '';
$categories ??= [];
$csrfToken ??= $_SESSION['csrf_token'] ?? '';
$flash ??= [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <style>
        table, th, td { border:1px solid #ccc; border-collapse:collapse; padding:6px; }
        .success { color:green; }
        .error { color:red; }
    </style>
</head>
<body>
    <h1>Products</h1>

    <?php if(!empty($flash['success'])): ?>
        <div class="success"><?= htmlspecialchars($flash['success']) ?></div>
    <?php endif; ?>
    <?php if(!empty($flash['error'])): ?>
        <div class="error"><?= htmlspecialchars($flash['error']) ?></div>
    <?php endif; ?>

    <form method="GET">
        <input type="hidden" name="route" value="products/index">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search...">
        <button>Search</button>
    </form>

    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p->getId() ?></td>
                <td><?= htmlspecialchars($p->getName()) ?></td>
                <td>$<?= number_format($p->getPrice(), 2) ?></td>
                <td><?= htmlspecialchars($categories[$p->getCategoryId()] ?? 'None') ?></td>
                <td>
                    <a href="index.php?route=products/show/<?= $p->getId() ?>">View</a>
                    <a href="index.php?route=products/edit/<?= $p->getId() ?>">Edit</a>
                    <form method="POST" action="index.php?route=products/delete/<?= $p->getId() ?>" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <button onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
                <tr><td colspan="5">No products found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?route=products/index&page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</body>
</html>
```

### `views\product\show.php`

```php
<?php
$product ??= null;
if (!$product) die('Product not found.');
$categoryName ??= null;
$flash ??= [];
?>
<!DOCTYPE html>
<html>
<head><title><?= htmlspecialchars($product->getName()) ?></title></head>
<body>
    <h1><?= htmlspecialchars($product->getName()) ?></h1>
    <?php if (!empty($flash['success'])): ?><div class="success"><?= $flash['success'] ?></div><?php endif; ?>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($product->getDescription())) ?></p>
    <p><strong>Price:</strong> $<?= number_format($product->getPrice(), 2) ?></p>
    <p><strong>Category:</strong> <?= htmlspecialchars($categoryName ?? 'None') ?></p>
    <?php if ($product->getImagePath()): ?>
        <img src="<?= htmlspecialchars($product->getImagePath()) ?>" style="max-width:200px">
    <?php endif; ?>
    <p><a href="index.php?route=products/edit/<?= $product->getId() ?>">Edit</a> | <a href="index.php?route=products/index">Back</a></p>
</body>
</html>
```

### `views\search\index.php`

```php
<?php
// views/search/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Search Engine</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h1 { color: #333; }
        .search-container { margin-bottom: 25px; padding: 15px; border: 1px solid #ccc; background: #f9f9f9; border-radius: 5px; }
        .grid-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .grid-table th, .grid-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .grid-table th { background: #eee; }
        .form-group { margin-bottom: 10px; display: inline-block; margin-right: 20px; }
        .error-flash { color: red; font-weight: bold; }
        button, a.btn { padding: 5px 10px; background: #0066cc; color: #fff; text-decoration: none; border: none; cursor: pointer; border-radius: 3px; font-size: 14px;}
        a.btn-outline { background: #eee; color: #333; border: 1px solid #ccc; text-decoration: none; padding: 4px 8px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Data Search Module</h1>
    
    <div class="search-container">
        <form action="index.php" method="GET">
            <input type="hidden" name="route" value="search/search">
            
            <div class="form-group">
                <label>Keyword:</label><br>
                <input type="text" name="keyword" value="<?= htmlspecialchars($criteria['keyword'] ?? '') ?>" placeholder="Search...">
            </div>

            <div class="form-group">
                <label>Category:</label><br>
                <select name="category">
                    <option value="">-- All Categories --</option>
                    <?php if(!empty($categories)): foreach($categories as $cat): ?>
                        <option value="<?= $cat->getId() ?>" <?= ($criteria['category_id'] == $cat->getId()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat->getName()) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Min Price:</label><br>
                <input type="number" step="0.01" name="price_min" value="<?= htmlspecialchars((string)($criteria['price_min'] ?? '')) ?>" style="width: 80px;">
            </div>

            <div class="form-group">
                <label>Max Price:</label><br>
                <input type="number" step="0.01" name="price_max" value="<?= htmlspecialchars((string)($criteria['price_max'] ?? '')) ?>" style="width: 80px;">
            </div>

            <div class="form-group">
                <button type="submit">Filter Results</button>
                <a href="index.php?route=search/search" class="btn-outline">Reset</a>
            </div>
            
            <div class="form-group" style="float: right;">
                <a href="index.php?route=auth/dashboard" class="btn-outline">Dashboard</a>
            </div>
        </form>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <p class="error-flash"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <table class="grid-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td>
                        <?php if(!empty($p['image_path'])): ?>
                            <img src="<?= htmlspecialchars($p['image_path']) ?>" width="40" alt="img">
                        <?php else: ?>
                            <span>No Img</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td>
                        <a href="index.php?route=products/show/<?= $p['id'] ?>">View Detail</a>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="6">No records matched the advanced filter.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div style="margin-top: 20px;">
            <?php 
                $queryParams = $_GET;
                unset($queryParams['page']);
                $queryString = http_build_query($queryParams);
                
                for ($i = 1; $i <= $totalPages; $i++): 
            ?>
                <a href="index.php?<?= $queryString ?>&page=<?= $i ?>" <?= ($page == $i) ? 'style="font-weight:bold; text-decoration:none; padding:4px; background:#ddd;"' : 'style="padding:4px;"' ?>><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

</body>
</html>
```

