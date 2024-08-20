<?php

namespace App\Repository;

use App\Entity\Player;
use App\Entity\Training;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Player>
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    //    /**
    //     * @return Player[] Returns an array of Player objects
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

    //    public function findOneBySomeField($value): ?Player
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function findNextTrainingForPlayer(Player $player): ?Training
    {
        $qb = $this->createQueryBuilder('p')
            ->select('t')
            ->from(Training::class, 't')
            ->join('t.category', 'c')
            ->where(':player MEMBER OF c.players')
            ->andWhere('(t.trainingAt > :now OR (t.trainingAt = :today AND t.trainingHour > :currentHour))')
            ->setParameter('player', $player)
            ->setParameter('now', new \DateTime())
            ->setParameter('today', (new \DateTime())->format('Y-m-d'))
            ->setParameter('currentHour', (new \DateTime())->format('H:i:s'))
            ->orderBy('t.trainingAt', 'ASC')
            ->setMaxResults(1);
    
        return $qb->getQuery()->getOneOrNullResult();
    }
    
}
