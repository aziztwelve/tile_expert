<?php

declare(strict_types=1);

namespace App\Modules\Order\EventSubscriber;

use App\Modules\Order\Event\OrderCreatedEvent;
use App\Modules\Search\Service\ManticoreSearchOrderService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class OrderEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ManticoreSearchOrderService $searchService,
        private LoggerInterface             $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderCreatedEvent::NAME => 'onOrderCreated',
        ];
    }

    public function onOrderCreated(OrderCreatedEvent $event): void
    {
        try {
            $order = $event->getOrder();

            $this->logger->info('Indexing order in Manticore', [
                'order_id' => $order->getId(),
                'order_hash' => $order->getHash(),
            ]);

            $this->searchService->indexOrder($order);

            $this->logger->info('Order successfully indexed', [
                'order_id' => $order->getId(),
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to index order in Manticore', [
                'order_id' => $event->getOrder()->getId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
