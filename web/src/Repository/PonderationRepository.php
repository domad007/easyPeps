<?php

namespace App\Repository;

use App\Entity\Ponderation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ponderation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ponderation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ponderation[]    findAll()
 * @method Ponderation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PonderationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ponderation::class);
    }

    // /**
    //  * @return Ponderation[] Returns an array of Ponderation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ponderation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
