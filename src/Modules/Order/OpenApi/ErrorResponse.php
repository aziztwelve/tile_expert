<?php

declare(strict_types=1);

namespace App\Modules\Order\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ErrorResponse',
    required: ['status', 'message'],
    properties: [
        new OA\Property(property: 'status', type: 'string', example: 'error'),
        new OA\Property(property: 'message', type: 'string', example: 'An error occurred'),
    ],
    type: 'object'
)]
class ErrorResponse
{
}
