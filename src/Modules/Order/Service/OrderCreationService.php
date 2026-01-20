<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Common\Repository\ArticleRepository;
use App\Modules\Common\Repository\CountryRepository;
use App\Modules\Order\Dto\CreateOrderDto;
use App\Modules\Order\Entity\Order;
use App\Modules\Order\Entity\OrderAddress;
use App\Modules\Order\Entity\OrderCarrier;
use App\Modules\Order\Entity\OrderDelivery;
use App\Modules\Order\Entity\OrderItem;
use App\Modules\Order\Entity\OrderPayment;
use App\Modules\Order\Enum\OrderStatus;
use App\Modules\Order\Event\OrderCreatedEvent;
use App\Modules\Order\Exception\ArticleNotFoundException;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\User\Repository\UserRepository;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class OrderCreationService
{
    public function __construct(
        private OrderRepository   $orderRepository,
        private ArticleRepository $articleRepository,
        private CountryRepository $countryRepository,
        private UserRepository    $userRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function createOrder(CreateOrderDto $dto): Order
    {
        $order = new Order();
        $order
            ->setHash($this->generateOrderHash())
            ->setName($dto->name)
            ->setLocale($dto->locale)
            ->setCurrency($dto->currency)
            ->setMeasure($dto->measure)
            ->setDiscountPercent($dto->discount_percent)
            ->setStatus(OrderStatus::NEW);

        $this->addItems($order, $dto->items);
        $this->setManager($order, $dto->manager);
        $this->setCarrier($order, $dto->carrier);
        $this->addAddresses($order, $dto->addresses);
        $this->setDelivery($order, $dto->delivery);
        $this->setPayment($order, $dto->payment);

        $this->orderRepository->save($order, true);

        $this->eventDispatcher->dispatch(
            new OrderCreatedEvent($order),
            OrderCreatedEvent::NAME
        );

        return $order;
    }

    private function generateOrderHash(): string
    {
        return md5(uniqid('', true));
    }

    private function addItems(Order $order, array $itemsData): void
    {
        foreach ($itemsData as $itemData) {
            $article = $this->articleRepository->find($itemData['article_id']);

            if (!$article) {
                throw new ArticleNotFoundException(
                    sprintf('Article with ID %s not found', $itemData['article_id'])
                );
            }

            $item = new OrderItem();
            $item
                ->setArticle($article)
                ->setQuantity($itemData['quantity'])
                ->setUnitPrice($itemData['unit_price'])
                ->setWeight($article->getWeight());

            $order->addItem($item);
        }
    }

    private function setManager(Order $order, ?array $managerData): void
    {
        if (empty($managerData['id'])) {
            return;
        }

        $manager = $this->userRepository->find((int) $managerData['id']);

        if (!$manager) {
            return;
        }

        if ($manager->getRole() === 'ROLE_MANAGER') {
            $order->setManager($manager);
        }
    }

    private function setCarrier(Order $order, ?array $carrierData): void
    {
        if (empty($carrierData)) {
            return;
        }

        $carrier = new OrderCarrier($order);
        $carrier
            ->setOrder($order)
            ->setName($carrierData['name'] ?? null)
            ->setContactData($carrierData['contact_data'] ?? null);

        $order->setCarrier($carrier);
    }

    private function addAddresses(Order $order, array $addressesData): void
    {
        foreach ($addressesData as $addressData) {
            $country = $this->countryRepository->find($addressData['country_id']);

            $address = new OrderAddress();
            $address
                ->setOrder($order)
                ->setType($addressData['type'])
                ->setCountry($country)
                ->setCity($addressData['city'] ?? null)
                ->setAddress($addressData['address'] ?? null);

            $order->addAddress($address);
        }
    }

    private function setDelivery(Order $order, ?array $deliveryData): void
    {
        if (empty($deliveryData)) {
            return;
        }

        $delivery = new OrderDelivery($order);
        $delivery
            ->setType($deliveryData['type'])
            ->setPrice($deliveryData['price'] ?? null);

        $order->setDelivery($delivery);
    }

    private function setPayment(Order $order, ?array $paymentData): void
    {
        if (empty($paymentData)) {
            return;
        }

        $payment = new OrderPayment($order);
        $payment
            ->setPayType($paymentData['pay_type'])
            ->setBankTransferRequested(
                (bool) ($paymentData['bank_transfer_requested'] ?? false)
            );

        $order->setPayment($payment);
    }
}
