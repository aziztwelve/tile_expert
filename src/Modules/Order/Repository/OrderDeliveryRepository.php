<?php

declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Order\Entity\OrderDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderDelivery>
 */
class OrderDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDelivery::class);
    }
}
