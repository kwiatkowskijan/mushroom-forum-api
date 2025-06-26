<?php

namespace App\Repository;

use App\Entity\UserMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserMetadata>
 */
class UserMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMetadata::class);
    }

    public function findAllForUser(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findOneForUser(int $userId, int $metadataId): ?UserMetadata
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.user = :userId')
            ->andWhere('m.id = :metadataId')
            ->setParameter('userId', $userId)
            ->setParameter('metadataId', $metadataId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
