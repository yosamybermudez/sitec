<?php

namespace App\Repository;

use App\Entity\MateriaPrimaEntrada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MateriaPrimaEntrada|null find($id, $lockMode = null, $lockVersion = null)
 * @method MateriaPrimaEntrada|null findOneBy(array $criteria, array $orderBy = null)
 * @method MateriaPrimaEntrada[]    findAll()
 * @method MateriaPrimaEntrada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MateriaPrimaEntradaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MateriaPrimaEntrada::class);
    }

    public function findByDates($inicio, $fin)
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.created', 'ASC');
        if(isset($inicio) and $inicio !== null){
            $inicio->setTime(0,0,0);
            $qb->andWhere('o.created > :inicio')
                ->setParameter('inicio', $inicio);
        }
        if(isset($fin) and $fin !== null){
            $fin->setTime(23,59,59);
            $qb->andWhere('o.created < :fin')
                ->setParameter('fin', $fin);
        }
        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return MateriaPrimaEntrada[] Returns an array of MateriaPrimaEntrada objects
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
    public function findOneBySomeField($value): ?MateriaPrimaEntrada
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
