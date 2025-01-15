<?php

namespace App\Repository;

use App\Entity\Encounter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Encounter>
 */
class EncounterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Encounter::class);
    }

    public function findOverlappingEncounters(
        int $fieldId,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        ?int $excludeId = null
    ): array {
        $qb = $this->createQueryBuilder('e')
            ->join('e.timeSlot', 'ts')
            ->where('e.field = :fieldId')
            ->andWhere('
                (ts.dateBegin BETWEEN :start AND :end) OR
                (ts.dateEnd BETWEEN :start AND :end) OR
                (:start BETWEEN ts.dateBegin AND ts.dateEnd) OR
                (:end BETWEEN ts.dateBegin AND ts.dateEnd)
            ')
            ->setParameters([
                'fieldId' => $fieldId,
                'start' => $startDate,
                'end' => $endDate
            ]);

        if ($excludeId !== null) {
            $qb->andWhere('e.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getResult();
    }

    public function countTournamentEncounters(int $tournamentId): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.tournament = :tournamentId')
            ->setParameter('tournamentId', $tournamentId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
