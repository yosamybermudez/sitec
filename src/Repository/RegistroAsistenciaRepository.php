<?php

namespace App\Repository;

use App\Entity\RegistroAsistencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RegistroAsistencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegistroAsistencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegistroAsistencia[]    findAll()
 * @method RegistroAsistencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistroAsistenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistroAsistencia::class);
    }

    // /**
    //  * @return RegistroAsistencia[] Returns an array of RegistroAsistencia objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RegistroAsistencia
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
