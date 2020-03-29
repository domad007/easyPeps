<?php

namespace App\Repository;

use App\Entity\Presences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Presences|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presences|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presences[]    findAll()
 * @method Presences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presences::class);
    }

    // /**
    //  * @return Presences[] Returns an array of Presences objects
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
    public function findOneBySomeField($value): ?Presences
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
