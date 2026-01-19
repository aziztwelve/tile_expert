<?php

namespace App\Modules\Order\Entity;

use App\Modules\Order\Repository\OrderDeliveryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderDeliveryRepository::class)]
#[ORM\Table(name: 'order_delivery')]
class OrderDelivery
{
    #[ORM\Id]
    #[ORM\OneToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Column(type: 'smallint')]
    private int $type = 0;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $warehouseData = null;

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getWarehouseData(): ?array
    {
        return $this->warehouseData;
    }

    public function setWarehouseData(?array $warehouseData): self
    {
        $this->warehouseData = $warehouseData;
        return $this;
    }
}
