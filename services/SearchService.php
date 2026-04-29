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
