<?php

namespace App\Modules\Order\OpenApi;

namespace App\Modules\Order\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderResponse',
    required: ['id', 'hash', 'status', 'locale', 'currency', 'measure', 'name', 'created_at'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'hash', type: 'string', example: 'a1b2c3d4e5f6'),
        new OA\Property(property: 'number', type: 'string', example: 'ORD-0001', nullable: true),
        new OA\Property(property: 'status', type: 'string', example: 'new'),
        new OA\Property(property: 'locale', type: 'string', example: 'de'),
        new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
        new OA\Property(property: 'measure', type: 'string', example: 'm'),
        new OA\Property(property: 'discount_percent', type: 'integer', example: 10, nullable: true),
        new OA\Property(property: 'name', type: 'string', example: 'Test order'),
        new OA\Property(property: 'description', type: 'string', example: 'Order description', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-20T10:30:00+00:00'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-21T15:45:00+00:00', nullable: true),
        new OA\Property(
            property: 'manager',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 5),
                new OA\Property(property: 'email', type: 'string', example: 'manager@example.com'),
                new OA\Property(property: 'name', type: 'string', example: 'John Manager'),
            ],
            type: 'object',
            nullable: true
        ),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'article',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 10),
                            new OA\Property(property: 'sku', type: 'string', example: 'ART-001'),
                            new OA\Property(property: 'name', type: 'string', example: 'Product Name'),
                        ],
                        type: 'object'
                    ),
                    new OA\Property(property: 'quantity', type: 'string', example: '25.000'),
                    new OA\Property(property: 'unit_price', type: 'string', example: '12.50'),
                    new OA\Property(property: 'unit_price_eur', type: 'string', nullable: true, example: '11.25'),
                    new OA\Property(property: 'weight', type: 'string', example: '18.500'),
                    new OA\Property(property: 'swimming_pool', type: 'boolean', example: false),
                ],
                type: 'object'
            )
        ),
        new OA\Property(
            property: 'addresses',
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'type', type: 'string', example: 'delivery'),
                    new OA\Property(
                        property: 'country',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'code', type: 'string', example: 'DE'),
                            new OA\Property(property: 'name', type: 'string', example: 'Germany'),
                        ],
                        type: 'object',
                        nullable: true
                    ),
                    new OA\Property(property: 'city', type: 'string', nullable: true, example: 'Berlin'),
                    new OA\Property(property: 'address', type: 'string', nullable: true, example: 'Alexanderplatz 1'),
                ],
                type: 'object'
            )
        ),
        new OA\Property(
            property: 'delivery',
            properties: [
                new OA\Property(property: 'type', type: 'integer', example: 0),
                new OA\Property(property: 'price', type: 'string', nullable: true, example: '120.00'),
                new OA\Property(property: 'warehouse_data', type: 'object', nullable: true, example: null),
            ],
            type: 'object',
            nullable: true
        ),
        new OA\Property(
            property: 'payment',
            properties: [
                new OA\Property(property: 'pay_type', type: 'integer', example: 1),
                new OA\Property(property: 'bank_transfer_requested', type: 'boolean', example: false),
                new OA\Property(property: 'cur_rate', type: 'string', example: '1.0000'),
            ],
            type: 'object',
            nullable: true
        ),
        new OA\Property(
            property: 'carrier',
            properties: [
                new OA\Property(property: 'name', type: 'string', nullable: true, example: 'DHL'),
                new OA\Property(property: 'contact_data', type: 'string', nullable: true, example: 'support@dhl.com'),
            ],
            type: 'object',
            nullable: true
        ),
    ],
    type: 'object'
)]
class OrderResponse
{
}
