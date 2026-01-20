<?php

declare(strict_types=1);

namespace App\Modules\Order\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateOrderRequest',
    required: ['name', 'locale', 'currency', 'measure', 'items', 'addresses'],
    properties: [
        new OA\Property(
            property: 'name',
            type: 'string',
            maxLength: 200,
            example: 'Order from API'
        ),
        new OA\Property(
            property: 'locale',
            type: 'string',
            maxLength: 5,
            example: 'de'
        ),
        new OA\Property(
            property: 'currency',
            type: 'string',
            maxLength: 3,
            example: 'EUR'
        ),
        new OA\Property(
            property: 'measure',
            type: 'string',
            maxLength: 3,
            example: 'm'
        ),
        new OA\Property(
            property: 'discount_percent',
            type: 'integer',
            maximum: 100,
            minimum: 0,
            example: 5,
            nullable: true
        ),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(
                required: ['article_id', 'quantity', 'unit_price'],
                properties: [
                    new OA\Property(property: 'article_id', type: 'integer', example: 37),
                    new OA\Property(property: 'quantity', type: 'string', example: '25.000'),
                    new OA\Property(property: 'unit_price', type: 'string', example: '12.50'),
                ],
                type: 'object'
            ),
            minItems: 1
        ),
        new OA\Property(
            property: 'addresses',
            type: 'array',
            items: new OA\Items(
                required: ['type', 'country_id'],
                properties: [
                    new OA\Property(property: 'type', type: 'string', example: 'delivery'),
                    new OA\Property(property: 'country_id', type: 'integer', example: 1),
                    new OA\Property(property: 'city', type: 'string', example: 'Berlin', nullable: true),
                    new OA\Property(property: 'address', type: 'string', example: 'Alexanderplatz 1', nullable: true),
                ],
                type: 'object'
            ),
            minItems: 1
        ),
        new OA\Property(
            property: 'delivery',
            properties: [
                new OA\Property(property: 'type', type: 'integer', example: 0),
                new OA\Property(property: 'price', type: 'string', nullable: true, example: '120.00'),
            ],
            type: 'object',
            nullable: true
        ),
        new OA\Property(
            property: 'payment',
            properties: [
                new OA\Property(property: 'pay_type', type: 'integer', example: 1),
                new OA\Property(property: 'bank_transfer_requested', type: 'boolean', example: false),
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
        new OA\Property(
            property: 'manager',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 38),
            ],
            type: 'object',
            nullable: true
        ),
    ],
    type: 'object'
)]
class CreateOrderRequest
{
}
