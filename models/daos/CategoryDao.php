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