<?php

namespace App\Repository;

use App\Entity\OrdenReparacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrdenReparacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdenReparacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdenReparacion[]    findAll()
 * @method OrdenReparacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdenReparacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdenReparacion::class);
    }

    public function findGastoMaterialesByDates($inicio, $fin){
        return $this->createQueryBuilder('o')
            ->andWhere('o.updated <= :fin')
            ->setParameter('fin', $fin)
            ->andWhere('o.updated >= :inicio')
            ->setParameter('inicio', $inicio)
            ->select('SUM(o.gastoMateriales), SUM(o.otrosGastos)')
            ->getQuery()
      //      ->getSingleScalarResult()
           ->getResult()
            ;
    }

    public function findByFechasTotalGastos($inicio, $fin)
    {
        try {
            $importe =  $this->createQueryBuilder('o')
                ->select('sum(o.gastoMateriales)')
                ->where('o.updated BETWEEN :inicio AND :fin')
                ->setParameter('fin', $fin)
                ->setParameter('inicio', $inicio)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (NoResultException $e){
            $importe = 0;
        }

        return $importe;

    }

    public function findByDates($inicio, $fin, $reparaciones = null)
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
        if($reparaciones){
            $qb
                ->andWhere("o.estadoFinal = 'R'");
        }
        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return OrdenReparacion[] Returns an array of OrdenReparacion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrdenReparacion
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
