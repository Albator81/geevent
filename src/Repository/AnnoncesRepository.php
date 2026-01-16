<?php

namespace App\Repository;

use App\Entity\Annonces;
use App\Enum\Target;
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

    public function betterFindAll(?int $start = null, ?int $maxResults = null, bool $isMember = true): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb->select([
            'a.id',
            'a.title',
            'a.content',
            'a.sent_at',
            'a.sent_to',
            'u.username as creator_username',
        ])
            ->join('a.sent_by', 'u')
            ->orderBy('a.sent_at', 'DESC');

        if (!$isMember) {
            $qb
                ->andWhere('a.sent_to = :target')
                ->setParameter('target', Target::ALL);
        }

        // Gestion de la limitation des résultats
        if (null !== $start && null !== $maxResults) {
            $qb->setFirstResult($start);
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getArrayResult();
    }

    public function countVisible(bool $isMember): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)');

        if (!$isMember) {
            $qb
                ->andWhere('a.sent_to = :target')
                ->setParameter('target', Target::ALL);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
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
    public function findLatestPublic(int $limit = 5): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.sent_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
