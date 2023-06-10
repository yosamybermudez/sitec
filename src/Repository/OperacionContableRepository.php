<?php

namespace App\Repository;

use App\Entity\OperacionContable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OperacionContable|null find($id, $lockMode = null, $lockVersion = null)
 * @method OperacionContable|null findOneBy(array $criteria, array $orderBy = null)
 * @method OperacionContable[]    findAll()
 * @method OperacionContable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperacionContableRepository extends ServiceEntityRepository
{

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

    public function nextNroOperacion($fecha = null)
    {
        $str = str_replace('/','-',$fecha);
        $fecha = new \DateTime($str);
        $fechaInicio = (clone $fecha)->setTime(0,0,0);
        $fechaFin = (clone $fecha)->setTime(23,59,59);
        try{
            $nro = $this->createQueryBuilder('o')
                ->select('o.nroOperacion')
                ->where('o.created >= :inicio and o.created <= :fin')
                ->setParameter('inicio', $fechaInicio)
                ->setParameter('fin', $fechaFin)
                ->orderBy('o.nroOperacion','DESC')
                ->setMaxResults(1)
                ->getQuery()->getSingleScalarResult();
            ;

        } catch (NoResultException $e)
        {
            $nro = null;
        }
        $nuevo = 'OC' . $fecha->format('Ymd') . $this->formarIdTresDigitos((int)substr($nro,10,3));
        return $nuevo;
    }

    private function formarIdTresDigitos(int $nro){
        $val = (string) ($nro + 1);
        $strlen = strlen($val);
        switch ($strlen){
            case 1: { return "00" . $val; }
            case 2: { return "0" . $val; }
            default: { return $val; }
        }
    }


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperacionContable::class);
    }

    // /**
    //  * @return OperacionContable[] Returns an array of OperacionContable objects
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
    public function findOneBySomeField($value): ?OperacionContable
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
