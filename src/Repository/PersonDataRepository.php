<?php

namespace App\Repository;

use App\Entity\PersonData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonData|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonData|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonData[]    findAll()
 * @method PersonData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonData::class);
    }

    // /**
    //  * @return PersonData[] Returns an array of PersonData objects
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
    public function findOneBySomeField($value): ?PersonData
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
