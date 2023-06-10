<?php

namespace App\Repository;

use App\Entity\ComprobanteOperacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ComprobanteOperacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComprobanteOperacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComprobanteOperacion[]    findAll()
 * @method ComprobanteOperacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComprobanteOperacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComprobanteOperacion::class);
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

    public function findByFechasTotalIngreso($inicio, $fin)
    {
        try {
            $importe =  $this->createQueryBuilder('o')
                ->select('sum(o.importeTotal)')
                ->where('o.updated BETWEEN :inicio AND :fin')
                ->andWhere("o.tipoOperacion = 'Cobro de servicio técnico'")
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

    public function nextNroComprobante($fecha = null)
    {
        $str = str_replace('/','-',$fecha);
        $fecha = new \DateTime($str);
        $fechaInicio = (clone $fecha)->setTime(0,0,0);
        $fechaFin = (clone $fecha)->setTime(23,59,59);
        try{
            $nro = $this->createQueryBuilder('o')
                ->select('o.nroComprobante')
                ->where('o.created >= :inicio and o.created <= :fin')
                ->setParameter('inicio', $fechaInicio)
                ->setParameter('fin', $fechaFin)
                ->orderBy('o.nroComprobante','DESC')
                ->setMaxResults(1)
                ->getQuery()->getSingleScalarResult();
            ;
        } catch (NoResultException $e)
        {
            $nro = null;
        }
        $nuevo = 'CO' . $fecha->format('Ymd') . $this->formarIdTresDigitos((int)substr($nro,10,3));
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

    // /**
    //  * @return ComprobanteOperacion[] Returns an array of ComprobanteOperacion objects
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
    public function findOneBySomeField($value): ?ComprobanteOperacion
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
