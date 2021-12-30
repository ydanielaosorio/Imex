<?php

namespace App\Repository;

use App\Entity\ContactoEmergencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactoEmergencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactoEmergencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactoEmergencia[]    findAll()
 * @method ContactoEmergencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactoEmergenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactoEmergencia::class);
    }

    // /**
    //  * @return ContactoEmergencia[] Returns an array of ContactoEmergencia objects
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
    public function findOneBySomeField($value): ?ContactoEmergencia
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
