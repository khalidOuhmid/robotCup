<?php

namespace App\Repository;

use App\Entity\TTeamTem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TTeamTem>
 */
class TTeamTemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TTeamTem::class);
    }

    public function findByOrderedByScore(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM v_team_statistics ORDER BY total_points DESC, matches_won DESC, total_goals DESC';
        
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }

    public function findWithStatistics(TTeamTem $team): ?TTeamTem
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT * FROM team_statistics 
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
    //     * @return TTeamTem[] Returns an array of TTeamTem objects
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

    //    public function findOneBySomeField($value): ?TTeamTem
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
