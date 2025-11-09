<?php

namespace App\Repository;

use App\Entity\Events;
use App\Entity\Participants;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participants>
 */
class ParticipantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participants::class);
    }

    public function existsByUserAndEvent(User $user, Events $event): bool
    {
        return (bool) $this->createQueryBuilder('p')
            ->select('1')
            ->where('p.participant = :user')
            ->andWhere('p.event = :event')
            ->setParameter('user', $user)
            ->setParameter('event', $event)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Participants[] Returns an array of Participants objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Participants
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
