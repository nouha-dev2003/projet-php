<?php
// controllers/ApiController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/ProductDao.php';
require_once __DIR__ . '/../models/daos/CategoryDao.php';
require_once __DIR__ . '/../services/SearchService.php';

use Config\Database;

class ApiController
{
    private ProductDao $productDao;
    private CategoryDao $categoryDao;
    private SearchService $searchService;

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->productDao = new ProductDao($pdo);
        $this->categoryDao = new CategoryDao($pdo);
        $this->searchService = new SearchService($pdo);
    }

    private function jsonResponse(array $data, int $statusCode = 200)
    {
        ob_clean(); // Ensure no HTTP buffer HTML leaked
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public function products()
    {
        // Add basic stateless authentication checking mapping later if gating.
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $products = $this->productDao->findPaginated($limit, $offset);
        
        $data = [];
        foreach ($products as $p) {
            $data[] = [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'price' => $p->getPrice(),
                'category_id' => $p->getCategoryId(),
                'image_path' => $p->getImagePath(),
                'created_at' => $p->getCreatedAt()
            ];
        }

        $this->jsonResponse([
            'status' => 'success',
            'count' => count($data),
            'data' => $data
        ]);
    }

    public function categories()
    {
        $categories = $this->categoryDao->findAll();
        
        $data = [];
        foreach ($categories as $c) {
            $data[] = [
                'id' => $c->getId(),
                'name' => $c->getName(),
                'slug' => $c->getSlug()
            ];
        }

        $this->jsonResponse([
            'status' => 'success',
            'count' => count($data),
            'data' => $data
        ]);
    }

    public function search()
    {
        $criteria = [
            'keyword'     => trim($_GET['keyword'] ?? ''),
            'category_id' => !empty($_GET['category']) ? (int) $_GET['category'] : null,
            'price_min'   => !empty($_GET['price_min']) ? (float) $_GET['price_min'] : null,
            'price_max'   => !empty($_GET['price_max']) ? (float) $_GET['price_max'] : null,
        ];

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;

        $result = $this->searchService->search($criteria, $page, $limit);
        
        if ($result['error']) {
            $this->jsonResponse(['status' => 'error', 'message' => $result['error']], 500);
        }

        $this->jsonResponse([
            'status' => 'success',
            'page' => $page,
            'limit' => $limit,
            'data' => $result['data']
        ]);
    }
}
