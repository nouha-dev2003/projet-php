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

// 1. Autoloader Composer (removed, dependencies loaded manually in controllers)

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