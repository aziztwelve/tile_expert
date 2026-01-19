<?php

namespace App\Modules\Order\Repository;

use App\Modules\Order\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getGroupedStats(
        string $group,
        int $page,
        int $limit
    ): array {
        $conn = $this->getEntityManager()->getConnection();

        $offset = ($page - 1) * $limit;

        $formats = [
            'day'   => 'YYYY-MM-DD',
            'month' => 'YYYY-MM',
            'year'  => 'YYYY',
        ];

        $format = $formats[$group] ?? $formats['month'];

        $sql = "
            SELECT
                TO_CHAR(created_at, :format) AS period,
                COUNT(*) AS total
            FROM orders
            GROUP BY period
            ORDER BY period DESC
            LIMIT :limit OFFSET :offset
        ";

        $countSql = "
            SELECT COUNT(DISTINCT TO_CHAR(created_at, :format)) AS cnt
            FROM orders
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('format', $format);
        $stmt->bindValue('limit', $limit);
        $stmt->bindValue('offset', $offset);

        $data = $stmt->executeQuery()->fetchAllAssociative();

        $countStmt = $conn->prepare($countSql);
        $countStmt->bindValue('format', $format);

        $totalGroups = (int) $countStmt->executeQuery()->fetchOne();

        return [
            'data' => $data,
            'total_groups' => $totalGroups,
        ];
    }
}
