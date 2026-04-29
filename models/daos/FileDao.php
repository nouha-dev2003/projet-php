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