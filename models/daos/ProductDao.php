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