<?php

namespace App\Repository;

use App\Entity\CoursGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CoursGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoursGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoursGroupe[]    findAll()
 * @method CoursGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoursGroupe::class);
    }

    // /**
    //  * @return CoursGroupe[] Returns an array of CoursGroupe objects
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
    public function findOneBySomeField($value): ?CoursGroupe
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
