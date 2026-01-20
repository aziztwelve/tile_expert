<?php

declare(strict_types=1);

namespace App\Modules\Order\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateOrderResponse',
    required: ['status', 'data'],
    properties: [
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'hash', type: 'string', example: 'a1b2c3d4e5f6'),
                new OA\Property(property: 'status', type: 'string', example: 'New'),
                new OA\Property(property: 'number', type: 'string', example: 'ORD-0001', nullable: true),
                new OA\Property(property: 'name', type: 'string', example: 'Order from API'),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class CreateOrderResponse
{
}
