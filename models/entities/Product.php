<?php
// Product.php
class Product
{
    private int $id;
    private string $name;
    private string $description;
    private float $price;
    private ?int $categoryId;
    private string $imagePath;
    private string $createdAt;

    public function __construct(?array $data = null)
    {
        if ($data) {
            $this->setId($data['id'] ?? 0);
            $this->setName($data['name'] ?? '');
            $this->setDescription($data['description'] ?? '');
            $this->setPrice((float)($data['price'] ?? 0));
            $this->setCategoryId($data['category_id'] ?? null);
            $this->setImagePath($data['image_path'] ?? '');
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;
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