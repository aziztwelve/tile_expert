<?php

declare(strict_types=1);

namespace App\Modules\Order\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class PriceQueryDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $factory,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $collection,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $article,
    ) {
    }
}
