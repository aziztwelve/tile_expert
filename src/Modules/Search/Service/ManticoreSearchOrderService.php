<?php

declare(strict_types=1);

namespace App\Modules\Search\Service;

use App\Modules\Order\Entity\Order;
use App\Modules\Order\Enum\OrderStatus;
use App\Modules\Search\Response\OrderSearchResponse;
use Manticoresearch\Client;
use Manticoresearch\Table;

class ManticoreSearchOrderService
{
    private Client $client;
    private Table $ordersTable;

    public function __construct(
        private readonly string $manticoreHost = 'localhost',
        private readonly int $manticorePort = 9308
    ) {
        $this->client = new Client([
            'host' => $this->manticoreHost,
            'port' => $this->manticorePort,
        ]);

        $this->ordersTable = $this->client->table('orders');
    }

    /**
     * Индексация заказа в Manticore
     */
    public function indexOrder(Order $order): void
    {
        $document = $this->prepareOrderDocument($order);

        try {
            $this->ordersTable->replaceDocument($document, $order->getId());
        } catch (\Exception $e) {
            // Логирование ошибки
            error_log("Failed to index order {$order->getId()}: " . $e->getMessage());
        }
    }

    /**
     * Удаление заказа из индекса
     */
    public function deleteOrder(int $orderId): void
    {
        try {
            $this->ordersTable->deleteDocument($orderId);
        } catch (\Exception $e) {
            error_log("Failed to delete order {$orderId} from index: " . $e->getMessage());
        }
    }

    /**
     * Полнотекстовый поиск с фильтрами
     */
    public function advancedSearch(
        ?string $query = null,
        ?string $status = null,
        ?string $currency = null,
        int $limit = 20,
        int $offset = 0
    ): OrderSearchResponse
    {
        $queryBuilder = $this->ordersTable->search($query ?? '');

        if ($status !== null) {
            $queryBuilder->filter('status','equals', OrderStatus::valueFromLabel($status));
        }
        if ($currency !== null) {
            $queryBuilder->filter('status','equals', $currency);
        }

        $resultSet = $queryBuilder->limit($limit)->offset($offset)->get();

        return OrderSearchResponse::fromResultSet($resultSet);
    }

    /**
     * Подготовка документа для индексации
     */
    private function prepareOrderDocument(Order $order): array
    {
        return [
            'hash' => $order->getHash(),
            'number' => $order->getNumber() ?? '',
            'name' => $order->getName(),
            'description' => $order->getDescription() ?? '',
            'status' => $order->getStatus()->label(),
            'locale' => $order->getLocale(),
            'currency' => $order->getCurrency(),
            'measure' => $order->getMeasure(),
            'discount_percent' => $order->getDiscountPercent() ?? 0,
            'manager_id' => $order->getManager()?->getId() ?? 0,
            'created_at' => $order->getCreatedAt()->getTimestamp(),
            'updated_at' => $order->getUpdatedAt()?->getTimestamp() ?? 0,

            // Дополнительные поля для поиска
            'manager_name' => $order->getManager()?->getName() ?? '',

            // Агрегированные данные из items
            'items_count' => $order->getItems()->count(),
            'articles_names' => $this->getArticlesNames($order),
            'articles_skus' => $this->getArticlesSkus($order),
        ];
    }

    private function getArticlesNames(Order $order): string
    {
        $names = [];
        foreach ($order->getItems() as $item) {
            $names[] = $item->getArticle()->getName();
        }
        return implode(' ', $names);
    }

    private function getArticlesSkus(Order $order): string
    {
        $skus = [];
        foreach ($order->getItems() as $item) {
            $skus[] = $item->getArticle()->getSku();
        }
        return implode(' ', $skus);
    }
}
