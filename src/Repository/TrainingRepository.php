<?php

namespace App\Repository;

use DateTime;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Training;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Training>
 */
class TrainingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Training::class);
    }

    //    /**
    //     * @return Training[] Returns an array of Training objects
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

    //    public function findOneBySomeField($value): ?Training
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByUserCategories(User $user)
    {
        return $this->createQueryBuilder('t')
            ->join('t.category', 'c')
            ->where(':user MEMBER OF c.users')
            ->setParameter('user', $user)
            ->orderBy('t.trainingAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findNextTrainingByCategory(Category $category): ?Training
    {
        $today = (new DateTime())->setTime(0, 0, 0);
        
        return $this->createQueryBuilder('t')
            ->andWhere('t.category = :category')
            ->andWhere('t.trainingAt >= :today')
            ->setParameter('category', $category)
            ->setParameter('today', $today)
            ->orderBy('t.trainingAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
}
