<?php

namespace App\Repository;

use App\Entity\Paciente;
use App\Entity\PersonData;
use App\Entity\TipoDocumento;
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

    public function buscarPersonData($tipoDocumento, $documento){
        $personData = $this->getEntityManager()->createQuery(
            'SELECT td.id, pd.documento, pd.nombre, pd.telefono, pd.correo, pd.sexo, pd.direccion, pd.fechaNacimiento
            FROM App:PersonData pd join pd.tipoDocumento td
            WHERE td.id = :tipoDocumento AND pd.documento = :documento'
        )->setParameters([
            'tipoDocumento' => $tipoDocumento,
            'documento' => $documento
        ])->getResult();
        $personData = new PersonData($personData[0]['id'],$personData[0]['documento'], $personData[0]['nombre'], 
        $personData[0]['telefono'], $personData[0]['sexo'], $personData[0]['correo'], $personData[0]['direccion'], $personData[0]['fechaNacimiento']);
        return $personData;
    }

    public function editarPersonData($personDataEditar){
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $query = $queryBuilder->update('App:PersonData', 'pd')
                ->set('pd.nombre', ':nombre')
                ->set('pd.telefono', ':telefono')
                ->set('pd.correo', ':correo')
                ->set('pd.direccion', ':direccion')
                ->set('pd.sexo', ':sexo')
                ->set('pd.fechaNacimiento', ':fechaNacimiento')
                ->where('pd.documento = :documento and pd.tipoDocumento in (SELECT td.id FROM App:PersonData pd2 JOIN pd2.tipoDocumento td WHERE td.id = :tipoDocumento)')
                ->setParameters($personDataEditar)
                ->getQuery();
        
        return $query->execute();;
    }
}
