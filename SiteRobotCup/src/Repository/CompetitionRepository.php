<?php

namespace App\Repository;

use App\Entity\Competition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CompetitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competition::class);
    }

    public function findOverlappingCompetitions(\DateTimeInterface $startDate, \DateTimeInterface $endDate, ?int $excludeId = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('
                (c.cmpDateBegin BETWEEN :start AND :end) OR
                (c.cmpDateEnd BETWEEN :start AND :end) OR
                (:start BETWEEN c.cmpDateBegin AND c.cmpDateEnd) OR
                (:end BETWEEN c.cmpDateBegin AND c.cmpDateEnd)
            ')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        if ($excludeId !== null) {
            $qb->andWhere('c.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getResult();
    }
}
