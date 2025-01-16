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

    public function findOverlappingCompetitions(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('
                (c.cmpDateBegin <= :end AND c.cmpDateEnd >= :start)
            ')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        return $qb->getQuery()->getResult();
    }
}
