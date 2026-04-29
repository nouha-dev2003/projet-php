<?php
// File.php
class File
{
    private int $id;
    private string $originalName;
    private string $storedName;
    private string $mimeType;
    private int $size;
    private int $userId;
    private string $createdAt;

    public function __construct(?array $data = null)
    {
        if ($data) {
            $this->setId($data['id'] ?? 0);
            $this->setOriginalName($data['original_name'] ?? '');
            $this->setStoredName($data['stored_name'] ?? '');
            $this->setMimeType($data['mime_type'] ?? '');
            $this->setSize((int)($data['size'] ?? 0));
            $this->setUserId((int)($data['user_id'] ?? 0));
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

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;
        return $this;
    }

    public function getStoredName(): string
    {
        return $this->storedName;
    }

    public function setStoredName(string $storedName): self
    {
        $this->storedName = $storedName;
        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
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