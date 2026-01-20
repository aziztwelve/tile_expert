<?php

declare(strict_types=1);

namespace App\Modules\Search\Response;

use Manticoresearch\ResultSet;

final class OrderSearchResponse implements \JsonSerializable
{
    /** @var OrderSearchItem[] */
    private array $items = [];

    public function __construct(
        array $items,
        private int $total,
        private int $took
    ) {
        $this->items = $items;
    }

    public static function fromResultSet(ResultSet $resultSet): self
    {
        $items = [];

        /** @var array $hit */
        foreach ($resultSet as $hit) {
            $items[] = OrderSearchItem::fromManticore($hit);
        }

        return new self(
            items: $items,
            total: $resultSet->getTotal(),
            took: $resultSet->getTime()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'items' => $this->items,
            'total' => $this->total,
            'took'  => $this->took,
        ];
    }
}

