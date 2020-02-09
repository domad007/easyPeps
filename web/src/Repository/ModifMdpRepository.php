<?php

namespace App\Repository;

use App\Entity\ModifMdp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ModifMdp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModifMdp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModifMdp[]    findAll()
 * @method ModifMdp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModifMdpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModifMdp::class);
    }

    // /**
    //  * @return ModifMdp[] Returns an array of ModifMdp objects
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
    public function findOneBySomeField($value): ?ModifMdp
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
