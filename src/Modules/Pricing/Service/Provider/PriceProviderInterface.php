<?php

namespace App\Modules\Pricing\Service\Provider;

interface PriceProviderInterface
{
    public function getPrice(
        string $factory,
        string $collection,
        string $article
    ): ?float;
}
