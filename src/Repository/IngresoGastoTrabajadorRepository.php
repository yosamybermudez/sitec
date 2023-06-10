<?php

namespace App\Repository;

use App\Entity\IngresoGastoTrabajador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IngresoGastoTrabajador|null find($id, $lockMode = null, $lockVersion = null)
 * @method IngresoGastoTrabajador|null findOneBy(array $criteria, array $orderBy = null)
 * @method IngresoGastoTrabajador[]    findAll()
 * @method IngresoGastoTrabajador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngresoGastoTrabajadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IngresoGastoTrabajador::class);
    }

    // /**
    //  * @return IngresoGastoTrabajador[] Returns an array of IngresoGastoTrabajador objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IngresoGastoTrabajador
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
