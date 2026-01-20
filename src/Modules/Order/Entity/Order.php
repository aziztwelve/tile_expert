<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity;

use App\Modules\Order\Enum\OrderStatus;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\User\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 32, unique: true)]
    private string $hash;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $number = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $manager = null;

    #[ORM\Column(enumType: OrderStatus::class)]
    private OrderStatus $status;

    #[ORM\Column(length: 5)]
    private string $locale;

    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';

    #[ORM\Column(length: 3)]
    private string $measure = 'm';

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $discountPercent = null;

    #[ORM\Column(length: 200)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $items;
    #[ORM\OneToMany(
        targetEntity: OrderAddress::class,
        mappedBy: 'order',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $addresses;
    #[ORM\OneToOne(
        targetEntity: OrderDelivery::class,
        mappedBy: 'order',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private ?OrderDelivery $delivery = null;

    #[ORM\OneToOne(
        targetEntity: OrderPayment::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private ?OrderPayment $payment = null;

    #[ORM\OneToOne(
        targetEntity: OrderCarrier::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove']
    )]
    private ?OrderCarrier $carrier = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->addresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): self
    {
        $this->manager = $manager;
        return $this;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getMeasure(): string
    {
        return $this->measure;
    }

    public function setMeasure(string $measure): self
    {
        $this->measure = $measure;
        return $this;
    }

    public function getDiscountPercent(): ?int
    {
        return $this->discountPercent;
    }

    public function setDiscountPercent(?int $discountPercent): self
    {
        $this->discountPercent = $discountPercent;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }
        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }
        return $this;
    }


    /**
     * @return Collection<int, OrderAddress>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(OrderAddress $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setOrder($this);
        }

        return $this;
    }

    public function getDelivery(): ?OrderDelivery
    {
        return $this->delivery;
    }

    public function setDelivery(?OrderDelivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getPayment(): ?OrderPayment
    {
        return $this->payment;
    }

    public function setPayment(?OrderPayment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getCarrier(): ?OrderCarrier
    {
        return $this->carrier;
    }

    public function setCarrier(?OrderCarrier $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

}
