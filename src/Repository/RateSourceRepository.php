<?php

namespace App\Repository;

use App\Entity\RateSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RateSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method RateSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method RateSource[]    findAll()
 * @method RateSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RateSource::class);
    }

    // /**
    //  * @return RateSource[] Returns an array of RateSource objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RateSource
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
