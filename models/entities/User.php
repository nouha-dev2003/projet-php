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