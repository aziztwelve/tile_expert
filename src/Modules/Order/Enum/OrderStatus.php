<?php

declare(strict_types=1);

namespace App\Modules\Order\Enum;

/**
 * Статусы заказа
 */
enum OrderStatus: int
{
    case NEW = 1;
    case PAID = 2;
    case SHIPPED = 3;
    case CANCELLED = 4;

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'new',
            self::PAID => 'paid',
            self::SHIPPED => 'shipped',
            self::CANCELLED => 'cancelled',
        };
    }

    public static function fromLabel(string $label): self
    {
        return match (strtolower($label)) {
            'new'       => self::NEW,
            'paid'      => self::PAID,
            'shipped'   => self::SHIPPED,
            'cancelled' => self::CANCELLED,
            default => throw new \InvalidArgumentException("Unknown order status: {$label}")
        };
    }

    public static function valueFromLabel(string $label): string
    {
        return match (strtolower($label)) {
            'new'       => self::NEW->label(),
            'paid'      => self::PAID->label(),
            'shipped'   => self::SHIPPED->label(),
            'cancelled' => self::CANCELLED->label(),
            default => throw new \InvalidArgumentException(
                "Unknown order status label: {$label}"
            ),
        };
    }
}
