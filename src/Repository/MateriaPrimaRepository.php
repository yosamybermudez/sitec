<?php

namespace App\Repository;

use App\Entity\MateriaPrima;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MateriaPrima|null find($id, $lockMode = null, $lockVersion = null)
 * @method MateriaPrima|null findOneBy(array $criteria, array $orderBy = null)
 * @method MateriaPrima[]    findAll()
 * @method MateriaPrima[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MateriaPrimaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MateriaPrima::class);
    }

    public function findAllEnExistencia(){
        return $this->createQueryBuilder('m')
            ->andWhere('m.cantidad > 0')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return MateriaPrima[] Returns an array of MateriaPrima objects
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
    public function findOneBySomeField($value): ?MateriaPrima
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
