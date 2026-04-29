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
        
        // Défaut : controller = 'Auth', action = 'login'
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) : 'Auth';
        $actionName = !empty($segments[1]) ? $segments[1] : 'login';
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