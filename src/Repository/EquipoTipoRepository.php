<?php

namespace App\Repository;

use App\Entity\EquipoTipo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EquipoTipo|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipoTipo|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipoTipo[]    findAll()
 * @method EquipoTipo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipoTipoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipoTipo::class);
    }

    // /**
    //  * @return EquipoTipo[] Returns an array of EquipoTipo objects
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
    public function findOneBySomeField($value): ?EquipoTipo
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
