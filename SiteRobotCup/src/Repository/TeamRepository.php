<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function findByOrderedByScore(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM team_stats_with_forfeit ORDER BY total_points DESC, total_wins DESC';
        
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }

    public function findWithStatistics(Team $team): ?Team
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT * FROM team_stats_with_forfeit 
            WHERE team_id = :teamId
        ';
        
        $result = $conn->executeQuery($sql, [
            'teamId' => $team->getId()
        ])->fetchAssociative();

        if ($result) {
            $team->setStatistics($result);
        }

        return $team;
    }

    //    /**
    //     * @return Team[] Returns an array of Team objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Team
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
