<?php

declare(strict_types=1);

namespace App\Modules\Order\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(max: 5)]
    public string $locale;

    #[Assert\NotBlank]
    #[Assert\Length(max: 3)]
    public string $currency;

    #[Assert\NotBlank]
    #[Assert\Length(max: 3)]
    public string $measure;

    #[Assert\Range(min: 0, max: 100)]
    public ?int $discount_percent = null;

    #[Assert\NotBlank(message: 'Items are required')]
    #[Assert\Count(
        min: 1,
        minMessage: 'At least one item is required'
    )]
    #[Assert\All([
        new Assert\Collection([
            'article_id' => [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
            ],
            'quantity' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
            ],
            'unit_price' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
            ],
        ])
    ])]
    public array $items = [];

    #[Assert\NotBlank(message: 'Addresses are required')]
    #[Assert\Count(
        min: 1,
        minMessage: 'At least one address is required'
    )]
    #[Assert\All([
        new Assert\Collection([
            'type' => [
                new Assert\NotBlank(),
                new Assert\Choice(['delivery', 'payer']),
            ],
            'country_id' => [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
            ],
            'city' => [
                new Assert\Type('string'),
            ],
            'address' => [
                new Assert\Type('string'),
            ],
        ])
    ])]
    public array $addresses = [];

    #[Assert\When(
        expression: 'this.delivery !== null',
        constraints: [
            new Assert\Collection([
                'type' => [
                    new Assert\NotBlank(),
                    new Assert\Type('integer'),
                ],
                'price' => [
                    new Assert\Type('string'),
                ],
            ])
        ]
    )]
    public ?array $delivery = null;

    #[Assert\When(
        expression: 'this.payment !== null',
        constraints: [
            new Assert\Collection([
                'pay_type' => [
                    new Assert\NotBlank(),
                    new Assert\Type('integer'),
                ],
                'bank_transfer_requested' => [
                    new Assert\Type('bool'),
                ],
            ])
        ]
    )]
    public ?array $payment = null;

    #[Assert\When(
        expression: 'this.carrier !== null',
        constraints: [
            new Assert\Collection([
                'name' => [
                    new Assert\Type('string'),
                ],
                'contact_data' => [
                    new Assert\Type('string'),
                ],
            ])
        ]
    )]
    public ?array $carrier = null;

    #[Assert\When(
        expression: 'this.manager !== null',
        constraints: [
            new Assert\Collection([
                'id' => [
                    new Assert\NotBlank(),
                    new Assert\Type('integer'),
                ],
            ])
        ]
    )]
    public ?array $manager = null;
}
