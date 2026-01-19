<?php

namespace App\Modules\Order\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderStatsQuery
{
    public function __construct(
        #[Assert\Choice(choices: ['day', 'month', 'year'])]
        public string $group = 'month',

        #[Assert\Positive]
        public int $page = 1,

        #[Assert\Range(min: 1, max: 100)]
        public int $limit = 10,
    ) {
    }
}
