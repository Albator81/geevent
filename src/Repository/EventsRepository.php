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

    /**
     * Récupère une liste d'évènements optimisée sous forme de tableaux.
     * Inclut le nom du créateur et le décompte des sponsors/participants.
     * * @param string $search Chaîne de caractères pour filtrer par titre ou description.
     * @param int|null $start Index de départ (pour la pagination).
     * @param int|null $end Index de fin (utilisé pour calculer le nombre de résultats).
     * @return array Un tableau multidimensionnel (pas des objets Event).
     */
    public function betterFindAll(string $search, int $start = null, int $end = null): array
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select([
            'e.id',
            'e.title',
            'e.description',
            'e.start_datetime',
            'e.end_datetime',
            'e.longitude',
            'e.latitude',
            'e.type',
            'e.address',
            'e.cover',
            'c.username as creator_username',
            'COUNT(DISTINCT s.id) as sponsors_count',
            'COUNT(DISTINCT p.id) as participants_count',
        ])
            ->join('e.created_by', 'c')
            ->leftJoin('e.sponsors', 's')
            ->leftJoin('e.participants', 'p')
            ->groupBy('e.id')
            ->addGroupBy('c.username');

        // Système de recherche
        if ('' != $search) {
            $qb->where('e.title LIKE :search')
                ->orWhere('e.description LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // Gestion de la limitation des résultats
        if ($start !== null && $end !== null) {
            $qb->setFirstResult($start);
            $qb->setMaxResults($end - $start);
        }

        // Tri par date de fin décroissante et exécution de la requête
        return $qb->orderBy('e.end_datetime', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}