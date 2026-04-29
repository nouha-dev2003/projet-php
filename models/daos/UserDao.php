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