<?php

namespace App\Modules\Order\Entity;

use App\Modules\Order\Repository\OrderCarrierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderCarrierRepository::class)]
#[ORM\Table(name: 'order_carrier')]
class OrderCarrier
{
    #[ORM\Id]
    #[ORM\OneToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactData = null;

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getContactData(): ?string
    {
        return $this->contactData;
    }

    public function setContactData(?string $contactData): self
    {
        $this->contactData = $contactData;
        return $this;
    }
}
