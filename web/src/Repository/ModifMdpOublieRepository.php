<?php

namespace App\Repository;

use App\Entity\ModifMdpOublie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ModifMdpOublie|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModifMdpOublie|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModifMdpOublie[]    findAll()
 * @method ModifMdpOublie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModifMdpOublieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModifMdpOublie::class);
    }

    // /**
    //  * @return ModifMdpOublie[] Returns an array of ModifMdpOublie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ModifMdpOublie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
