<?php

declare(strict_types=1);

namespace App\Modules\Order\Controller;

use App\Modules\Order\Service\PriceScraperService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class PriceController extends AbstractController
{
    public function __construct(
        private readonly PriceScraperService $priceScraperService
    ) {}

    #[Route('/price', name: 'api_price_get', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/price',
        summary: 'Get tile price from tile.expert',
        tags: ['Price']
    )]
    #[OA\Parameter(
        name: 'factory',
        description: 'Factory name',
        in: 'query',
        required: true,
        example: 'cobsa'
    )]
    #[OA\Parameter(
        name: 'collection',
        description: 'Collection name',
        in: 'query',
        required: true,
        example: 'manual'
    )]
    #[OA\Parameter(
        name: 'article',
        description: 'Article code',
        in: 'query',
        required: true,
        example: 'manu7530bcbm-manualbaltic7-5x30'
    )]
    #[OA\Response(
        response: 200,
        description: 'Price information',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'price', type: 'number', example: 38.99),
                new OA\Property(property: 'factory', type: 'string', example: 'cobsa'),
                new OA\Property(property: 'collection', type: 'string', example: 'manual'),
                new OA\Property(property: 'article', type: 'string', example: 'manu7530bcbm-manualbaltic7-5x30')
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Missing required parameters'
    )]
    #[OA\Response(
        response: 404,
        description: 'Price not found'
    )]
    public function getPrice(Request $request): JsonResponse
    {
        //todo: add validation
        $factory = $request->query->get('factory');
        $collection = $request->query->get('collection');
        $article = $request->query->get('article');

        if (!$factory || !$collection || !$article) {
            return $this->json([
                'error' => 'Missing required parameters: factory, collection, article'
            ], 400);
        }

        try {
            $price = $this->priceScraperService->getPrice($factory, $collection, $article);

            if ($price === null) {
                return $this->json([
                    'error' => 'Price not found for the given parameters'
                ], 404);
            }

            return $this->json([
                'price' => $price,
                'factory' => $factory,
                'collection' => $collection,
                'article' => $article
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to fetch price',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
