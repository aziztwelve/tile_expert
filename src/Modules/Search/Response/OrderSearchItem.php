<?php

declare(strict_types=1);

namespace App\Modules\Search\Response;

use Manticoresearch\ResultHit;

final class OrderSearchItem
{
    public function __construct(
        public int $id,
        public string $hash,
        public string $status,
        public string $locale,
        public string $currency,
        public string $measure,
        public int $discountPercent,
        public int $managerId,
        public int $createdAt,
        public int $updatedAt,
        public int $itemsCount,
    ) {}

    public static function fromManticore(ResultHit $hit): self
    {
        $source = $hit->getData();

        return new self(
            id: $hit->getId(),
            hash: $source['hash'],
            status: $source['status'],
            locale: $source['locale'],
            currency: $source['currency'],
            measure: $source['measure'],
            discountPercent: (int) $source['discount_percent'],
            managerId: (int) $source['manager_id'],
            createdAt: (int) $source['created_at'],
            updatedAt: (int) $source['updated_at'],
            itemsCount: (int) $source['items_count'],
        );
    }
}

