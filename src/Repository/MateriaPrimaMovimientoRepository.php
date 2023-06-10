<?php

namespace App\Repository;

use App\Entity\MateriaPrimaMovimiento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MateriaPrimaMovimiento|null find($id, $lockMode = null, $lockVersion = null)
 * @method MateriaPrimaMovimiento|null findOneBy(array $criteria, array $orderBy = null)
 * @method MateriaPrimaMovimiento[]    findAll()
 * @method MateriaPrimaMovimiento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MateriaPrimaMovimientoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MateriaPrimaMovimiento::class);
    }

    public function findDistinctNroMovimiento(){
        return $this->createQueryBuilder('m')
            ->select('distinct(m.nroMovimiento)')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function nextNroMovimiento($fecha = null)
    {
        $str = str_replace('/','-',$fecha);
        $fecha = new \DateTime($str);
        $fechaInicio = (clone $fecha)->setTime(0,0,0);
        $fechaFin = (clone $fecha)->setTime(23,59,59);
        try{
            $nro = $this->createQueryBuilder('m')
                ->select('distinct(m.nroMovimiento)')
                ->where('m.created >= :inicio and m.created <= :fin')
                ->setParameter('inicio', $fechaInicio)
                ->setParameter('fin', $fechaFin)
                ->orderBy('m.nroMovimiento','DESC')
                ->setMaxResults(1)
                ->getQuery()->getSingleScalarResult();
            } catch (NoResultException $e)
        {
            $nro = null;
        }
        $nuevo = 'MOV' . $fecha->format('Ymd') . $this->formarIdTresDigitos((int)substr($nro,11,4));
        return $nuevo;
    }

    private function formarIdTresDigitos(int $nro){
        $val = (string) ($nro + 1);
        $strlen = strlen($val);
        switch ($strlen){
            case 1: { return "000" . $val; }
            case 2: { return "00" . $val; }
            case 3: { return "0" . $val; }
            default: { return $val; }
        }
    }

    public function findByDates($inicio, $fin)
    {
        $qb = $this->createQueryBuilder('o')
            ->select('distinct(o.nroMovimiento)');
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
    //  * @return MateriaPrimaMovimiento[] Returns an array of MateriaPrimaMovimiento objects
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
    public function findOneBySomeField($value): ?MateriaPrimaMovimiento
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
