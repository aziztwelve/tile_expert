<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity;

use App\Modules\Order\Repository\OrderPaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderPaymentRepository::class)]
#[ORM\Table(name: 'order_payment')]
class OrderPayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Order::class, inversedBy: 'payment')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Order $order;

    #[ORM\Column(type: 'smallint')]
    private int $payType;

    #[ORM\Column(type: 'boolean')]
    private bool $bankTransferRequested = false;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 4)]
    private string $curRate = '1';

    public function __construct(Order $order)
    {
        $this->order  = $order;

        $order->setPayment($this);
    }

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

    public function getPayType(): int
    {
        return $this->payType;
    }

    public function setPayType(int $payType): self
    {
        $this->payType = $payType;
        return $this;
    }

    public function isBankTransferRequested(): bool
    {
        return $this->bankTransferRequested;
    }

    public function setBankTransferRequested(bool $bankTransferRequested): self
    {
        $this->bankTransferRequested = $bankTransferRequested;
        return $this;
    }

    public function getCurRate(): string
    {
        return $this->curRate;
    }

    public function setCurRate(string $curRate): self
    {
        $this->curRate = $curRate;
        return $this;
    }
}
