<?php

namespace App\Repository;

use App\Entity\Events;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Events>
 */
class EventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Events::class);
    }

    public function betterFindAll(): array
    {
        $qb = $this->createQueryBuilder('e');

        return $qb->select([
                'e.id',
                'e.title',
                'e.description',
                'e.start_datetime',
                'e.end_datetime',
                'e.longitude',
                'e.latitude',
                'e.type',
                'e.address',
                'c.username as creator_username',
                'COUNT(DISTINCT s.id) as sponsors_count',
                'COUNT(DISTINCT p.id) as participants_count'
            ])
            ->join('e.created_by', 'c')
            ->leftJoin('e.sponsors', 's')
            ->leftJoin('e.participants', 'p')
            ->groupBy('e.id')
            ->addGroupBy('c.username')
            ->orderBy('e.end_datetime', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    //    /**
    //     * @return Events[] Returns an array of Events objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Events
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
