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
