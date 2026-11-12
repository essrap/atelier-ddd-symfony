<?php

declare(strict_types=1);

namespace App\Review\Repository;

use App\Review\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
final class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @return list<Review>
     */
    public function findForRental(string $rentalReference): array
    {
        return $this->findBy(['rentalReference' => $rentalReference], ['createdAt' => 'ASC', 'id' => 'ASC']);
    }
}
