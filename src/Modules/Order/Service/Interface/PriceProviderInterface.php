<?php

namespace App\Modules\Order\Service\Interface;

interface PriceProviderInterface
{
    public function getPrice(
        string $factory,
        string $collection,
        string $article
    ): ?float;
}
