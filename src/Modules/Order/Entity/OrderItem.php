<?php

namespace App\Modules\Order\Entity;

use App\Modules\Common\Entity\Article;
use App\Modules\Order\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\Table(name: 'order_items')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Article $article;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 3)]
    private string $quantity;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $unitPrice;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?string $unitPriceEur = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 3)]
    private string $weight;

    #[ORM\Column(type: 'boolean')]
    private bool $swimmingPool = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getArticle(): Article
    {
        return $this->article;
    }

    public function setArticle(Article $article): self
    {
        $this->article = $article;
        return $this;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function getUnitPriceEur(): ?string
    {
        return $this->unitPriceEur;
    }

    public function setUnitPriceEur(?string $unitPriceEur): self
    {
        $this->unitPriceEur = $unitPriceEur;
        return $this;
    }

    public function getWeight(): string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function isSwimmingPool(): bool
    {
        return $this->swimmingPool;
    }

    public function setSwimmingPool(bool $swimmingPool): self
    {
        $this->swimmingPool = $swimmingPool;
        return $this;
    }
}
