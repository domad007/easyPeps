<?php

namespace App\Repository;

use App\Entity\EleveSupprime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EleveSupprime|null find($id, $lockMode = null, $lockVersion = null)
 * @method EleveSupprime|null findOneBy(array $criteria, array $orderBy = null)
 * @method EleveSupprime[]    findAll()
 * @method EleveSupprime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EleveSupprimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EleveSupprime::class);
    }

    // /**
    //  * @return EleveSupprime[] Returns an array of EleveSupprime objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EleveSupprime
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
