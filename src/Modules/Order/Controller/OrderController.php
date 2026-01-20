<?php

declare(strict_types=1);

namespace App\Modules\Order\Controller;

use App\Modules\Common\Repository\ArticleRepository;
use App\Modules\Common\Repository\CountryRepository;
use App\Modules\Order\Dto\CreateOrderDto;
use App\Modules\Order\Dto\OrderStatsQueryDto;
use App\Modules\Order\Entity\Order;
use App\Modules\Order\Entity\OrderAddress;
use App\Modules\Order\Entity\OrderCarrier;
use App\Modules\Order\Entity\OrderDelivery;
use App\Modules\Order\Entity\OrderItem;
use App\Modules\Order\Entity\OrderPayment;
use App\Modules\Order\Enum\OrderStatus;
use App\Modules\Order\Exception\ArticleNotFoundException;
use App\Modules\Order\Exception\CountryNotFoundException;
use App\Modules\Order\OpenApi\CreateOrderRequest;
use App\Modules\Order\OpenApi\CreateOrderResponse;
use App\Modules\Order\OpenApi\ErrorResponse;
use App\Modules\Order\OpenApi\OrderResponse;
use App\Modules\Order\Repository\OrderRepository;
use App\Modules\Order\Service\ManticoreSearchService;
use App\Modules\Order\Service\OrderCreationService;
use App\Modules\User\Entity\User;
use App\Modules\User\Repository\UserRepository;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        #[MapQueryString] OrderStatsQueryDto $query,
        OrderRepository                      $orderRepository
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
    #[OA\Response(
        response: 200,
        description: 'Order details',
        content: new Model(type: OrderResponse::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Order not found'
    )]
    public function getOrder(int $id): JsonResponse
    {
        /** @var Order|null $order */
        $order = $this->orderRepository->findOneWithRelations($id);

        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        return $this->json([
            'id' => $order->getId(),
            'hash' => $order->getHash(),
            'number' => $order->getNumber(),
            'status' => $order->getStatus(),
            'locale' => $order->getLocale(),
            'currency' => $order->getCurrency(),
            'measure' => $order->getMeasure(),
            'discount_percent' => $order->getDiscountPercent(),
            'name' => $order->getName(),
            'description' => $order->getDescription(),
            'created_at' => $order->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $order->getUpdatedAt()?->format(DATE_ATOM),

            'manager' => $order->getManager() ? [
                'id' => $order->getManager()->getId(),
                'email' => $order->getManager()->getEmail(),
                'name' => $order->getManager()->getName(),
            ] : null,

            'items' => array_map(static function ($item) {
                return [
                    'id' => $item->getId(),
                    'article' => [
                        'id' => $item->getArticle()->getId(),
                        'sku' => $item->getArticle()->getSku(),
                        'name' => $item->getArticle()->getName(),
                    ],
                    'quantity' => $item->getQuantity(),
                    'unit_price' => $item->getUnitPrice(),
                    'unit_price_eur' => $item->getUnitPriceEur(),
                    'weight' => $item->getWeight(),
                    'swimming_pool' => $item->isSwimmingPool(),
                ];
            }, $order->getItems()->toArray()),

            'addresses' => array_map(static function ($address) {
                return [
                    'type' => $address->getType(),
                    'country' => $address->getCountry() ? [
                        'id' => $address->getCountry()->getId(),
                        'code' => $address->getCountry()->getCode(),
                        'name' => $address->getCountry()->getName(),
                    ] : null,
                    'city' => $address->getCity(),
                    'address' => $address->getAddress(),
                ];
            }, $order->getAddresses()->toArray()),

            'delivery' => $order->getDelivery() ? [
                'type' => $order->getDelivery()->getType(),
                'price' => $order->getDelivery()->getPrice(),
                'warehouse_data' => $order->getDelivery()->getWarehouseData(),
            ] : null,

            'payment' => $order->getPayment() ? [
                'pay_type' => $order->getPayment()->getPayType(),
                'bank_transfer_requested' => $order->getPayment()->isBankTransferRequested(),
                'cur_rate' => $order->getPayment()->getCurRate(),
            ] : null,

            'carrier' => $order->getCarrier() ? [
                'name' => $order->getCarrier()->getName(),
                'contact_data' => $order->getCarrier()->getContactData(),
            ] : null,
        ]);
    }

    #[Route('', name: 'api_orders_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/orders',
        summary: 'Create a new order',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateOrderRequest::class))
        ),
        tags: ['Orders']
    )]
    #[OA\Response(
        response: 201,
        description: 'Order created successfully',
        content: new OA\JsonContent(ref: new Model(type: CreateOrderResponse::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation error',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    example: ['name' => 'This value should not be blank.']
                ),
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Article not found',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class))
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponse::class))
    )]
    public function createOrder(
        #[MapRequestPayload] CreateOrderDto $createOrderDto,
        OrderCreationService $orderCreationService
    ): JsonResponse {
        try {
            $order = $orderCreationService->createOrder($createOrderDto);

            return $this->json([
                'status' => 'success',
                'data' => [
                    'id' => $order->getId(),
                    'hash' => $order->getHash(),
                    'status' => $order->getStatus()->label(),
                    'number' => $order->getNumber(),
                    'name' => $order->getName()
                ]
            ], 201);

        } catch (ArticleNotFoundException $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);

        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the order'
            ], 500);
        }
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
