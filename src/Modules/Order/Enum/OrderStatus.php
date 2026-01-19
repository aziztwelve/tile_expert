<?php

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
            self::NEW => 'New',
            self::PAID => 'Paid',
            self::SHIPPED => 'Shipped',
            self::CANCELLED => 'Cancelled',
        };
    }
}
