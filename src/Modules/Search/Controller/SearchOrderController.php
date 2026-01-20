<?php

declare(strict_types=1);

namespace App\Modules\Search\Controller;

use App\Modules\Search\Service\ManticoreSearchOrderService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/search', name: 'api_search_')]
#[OA\Tag(name: 'Search')]
class SearchOrderController extends AbstractController
{
    public function __construct(
        private readonly ManticoreSearchOrderService $searchService
    ) {
    }

    #[Route('/orders', name: 'orders', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/search/orders',
        summary: 'Search orders using Manticore',
        tags: ['Search']
    )]
    #[OA\Parameter(
        name: 'q',
        description: 'Search query',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string'),
        example: 'DHL'
    )]
    #[OA\Parameter(
        name: 'status',
        description: 'Filter by status',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string'),
        example: 'new'
    )]
    #[OA\Parameter(
        name: 'currency',
        description: 'Filter by currency',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string'),
        example: 'EUR'
    )]
    #[OA\Parameter(
        name: 'user_id',
        description: 'Filter by user ID',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Results per page',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 20),
        example: 20
    )]
    #[OA\Parameter(
        name: 'offset',
        description: 'Results offset',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 0),
        example: 0
    )]
    #[OA\Response(
        response: 200,
        description: 'Search results',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'total', type: 'integer', example: 42),
                        new OA\Property(
                            property: 'results',
                            type: 'array',
                            items: new OA\Items(type: 'object')
                        ),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        )
    )]
    public function searchOrders(Request $request): JsonResponse
    {
        try {
            $query = $request->query->get('q');
            $status = $request->query->get('status');
            $currency = $request->query->get('currency');
            $limit = $request->query->getInt('limit', 20);
            $offset = $request->query->getInt('offset', 0);

            $response = $this->searchService->advancedSearch(
                query: $query,
                status: $status,
                currency: $currency,
                limit: $limit,
                offset: $offset
            );

            return $this->json($response);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
