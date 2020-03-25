<?php

namespace App\Repository;

use App\Entity\Degre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Degre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Degre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Degre[]    findAll()
 * @method Degre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DegreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Degre::class);
    }

    // /**
    //  * @return Degre[] Returns an array of Degre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Degre
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
