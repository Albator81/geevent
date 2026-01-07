<?php

namespace App\Repository;

use App\Entity\Annonces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonces>
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

    public function betterFindAll(int $start = null, int $maxResults = null): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->select([
            'a.id',
            'a.title',
            'a.content',
            'a.sent_by',
            'a.sent_at',
            'u.username as creator_username',
        ])
            ->join('a.sent_by', 'u')
            ->orderBy('a.sent_at', 'DESC');


        // Gestion de la limitation des résultats
        if ($start !== null && $maxResults !== null) {
            $qb->setFirstResult($start);
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getArrayResult();
    }

    //    /**
    //     * @return Annonces[] Returns an array of Annonces objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Annonces
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
