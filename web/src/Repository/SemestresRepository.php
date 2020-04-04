<?php

namespace App\Repository;

use App\Entity\Semestres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Semestres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Semestres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Semestres[]    findAll()
 * @method Semestres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SemestresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Semestres::class);
    }

    // /**
    //  * @return Semestres[] Returns an array of Semestres objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Semestres
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
