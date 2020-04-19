<?php

namespace App\Repository;

use App\Entity\CustomizedPresences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CustomizedPresences|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomizedPresences|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomizedPresences[]    findAll()
 * @method CustomizedPresences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomizedPresencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomizedPresences::class);
    }

    // /**
    //  * @return CustomizedPresences[] Returns an array of CustomizedPresences objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CustomizedPresences
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
