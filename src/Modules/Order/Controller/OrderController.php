<?php

declare(strict_types=1);

namespace App\Modules\Order\Controller;

use App\Modules\Order\Dto\OrderStatsQuery;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Service\ManticoreSearchService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/orders')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ManticoreSearchService $manticoreService
    ) {}

    #[Route('/stats', name: 'api_orders_stats', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/orders/stats',
        summary: 'Get orders statistics with grouping',
        tags: ['Orders']
    )]
    #[OA\Parameter(
        name: 'group_by',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string', enum: ['day', 'month', 'year']),
        example: 'month'
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        schema: new OA\Schema(type: 'integer', default: 1),
        example: 1
    )]
    #[OA\Parameter(
        name: 'page_size',
        in: 'query',
        schema: new OA\Schema(type: 'integer', default: 10),
        example: 10
    )]
    #[Route('/stats', name: 'api_orders_stats', methods: ['GET'])]
    public function stats(
        #[MapQueryString] OrderStatsQuery $query,
        OrderRepository $orderRepository
    ): JsonResponse {
        $result = $orderRepository->getGroupedStats(
            group: $query->group,
            page: $query->page,
            limit: $query->limit
        );

        $totalPages = (int) ceil($result['total_groups'] / $query->limit);

        return $this->json([
            'meta' => [
                'group' => $query->group,
                'page' => $query->page,
                'limit' => $query->limit,
                'total_groups' => $result['total_groups'],
                'total_pages' => $totalPages,
            ],
            'data' => array_map(static fn ($row) => [
                'period' => $row['period'],
                'count'  => (int) $row['total'],
            ], $result['data']),
        ]);
    }

    #[Route('/{id}', name: 'api_orders_get', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/orders/{id}',
        summary: 'Get single order by ID',
        tags: ['Orders']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    public function getOrder(int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        return $this->json([
            'id' => $order->getId(),
            'customer_id' => $order->getCustomerId(),
            'customer_name' => $order->getCustomerName(),
            'customer_email' => $order->getCustomerEmail(),
            'product_name' => $order->getProductName(),
            'quantity' => $order->getQuantity(),
            'price' => $order->getPrice(),
            'total' => $order->getTotal(),
            'status' => $order->getStatus(),
            'created_at' => $order->getCreatedAt()->format('c'),
            'updated_at' => $order->getUpdatedAt()->format('c')
        ]);
    }

    #[Route('/search', name: 'api_orders_search', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/orders/search',
        summary: 'Search orders using Manticore Search',
        tags: ['Orders']
    )]
    #[OA\Parameter(
        name: 'q',
        description: 'Search query',
        in: 'query',
        required: true,
        example: 'Baltic'
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        schema: new OA\Schema(type: 'integer', default: 1),
        example: 1
    )]
    #[OA\Parameter(
        name: 'page_size',
        in: 'query',
        schema: new OA\Schema(type: 'integer', default: 10),
        example: 10
    )]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        $page = max(1, (int)$request->query->get('page', 1));
        $pageSize = max(1, min(100, (int)$request->query->get('page_size', 10)));

        if (!$query) {
            return $this->json(['error' => 'Missing search query parameter: q'], 400);
        }

        try {
            $results = $this->manticoreService->search($query, $page, $pageSize);
            return $this->json($results);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
