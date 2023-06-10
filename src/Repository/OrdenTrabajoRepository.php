<?php

namespace App\Repository;
use App\Entity\OrdenTrabajo;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use function GuzzleHttp\Psr7\str;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * @method OrdenTrabajo|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdenTrabajo|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdenTrabajo[]    findAll()
 * @method OrdenTrabajo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdenTrabajoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdenTrabajo::class);
    }

    public function findOrdenesIndex()
    {
        $fecha = new \DateTime();
        $fecha->modify("+1 day");
        $fecha->setTime(0,0,0);
        return $this->createQueryBuilder('o')
            ->andWhere('o.fechaEntrada <= :fin')
            ->setParameter('fin', $fecha)
            ->orderBy('o.nroOrden', 'DESC')
            ->getQuery()
            ->getResult()
            ;
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

    public function findByFechaEntrada($inicio, $fin)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.fechaEntrada <= :fin')
            ->setParameter('fin', $fin)
            ->andWhere('o.fechaEntrada >= :inicio')
            ->setParameter('inicio', $inicio)
            ->orderBy('o.fechaEntrada', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByFechaDictamen($inicio, $fin)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.fechaDictamen <= :fin')
            ->setParameter('fin', $fin)
            ->andWhere('o.fechaDictamen >= :inicio')
            ->setParameter('inicio', $inicio)
            ->orderBy('o.fechaDictamen', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByFechaSalida($inicio, $fin)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.fechaSalida <= :fin')
            ->setParameter('fin', $fin)
            ->andWhere('o.fechaSalida >= :inicio')
            ->setParameter('inicio', $inicio)
            ->orderBy('o.fechaSalida', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByFechaListoEntregar($inicio, $fin)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.fechaListoEntregar <= :fin')
            ->setParameter('fin', $fin)
            ->andWhere('o.fechaListoEntregar >= :inicio')
            ->setParameter('inicio', $inicio)
            ->orderBy('o.fechaListoEntregar', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByFechaNotificacion($inicio, $fin)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.fechaNotificacion <= :fin')
            ->setParameter('fin', $fin)
            ->andWhere('o.fechaNotificacion >= :inicio')
            ->setParameter('inicio', $inicio)
            ->orderBy('o.fechaNotificacion', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByFechaFacturacion($inicio, $fin)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.fechaFacturacion <= :fin')
            ->setParameter('fin', $fin)
            ->andWhere('o.fechaFacturacion >= :inicio')
            ->setParameter('inicio', $inicio)
            ->orderBy('o.fechaFacturacion', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByFechaDecomiso($inicio, $fin)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.fechaDecomiso <= :fin')
            ->setParameter('fin', $fin)
            ->andWhere('o.fechaDecomiso >= :inicio')
            ->setParameter('inicio', $inicio)
            ->orderBy('o.fechaDecomiso', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByTodasLasFechas($inicio, $fin)
    {
        return $this->createQueryBuilder('o')
            ->where('o.fechaEntrada BETWEEN :inicio AND :fin')
            ->orWhere('o.fechaDictamen BETWEEN :inicio AND :fin')
            ->orWhere('o.fechaSalida BETWEEN :inicio AND :fin')
            ->orWhere('o.fechaListoEntregar BETWEEN :inicio AND :fin')
            ->orWhere('o.fechaNotificacion BETWEEN :inicio AND :fin')
            ->orWhere('o.fechaFacturacion BETWEEN :inicio AND :fin')
            ->orWhere('o.fechaDecomiso BETWEEN :inicio AND :fin')
            ->setParameter('fin', $fin)
            ->setParameter('inicio', $inicio)
            ->orderBy('o.nroOrden', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
//
//    public function findTrabajosMostrarJornada(){
//        return $this->createQueryBuilder('o')
//            ->where("o.estado ='N'") //Notificado
//            ->orWhere("o.estado ='ECT'") //En cola para el tecnico
//            ->orWhere("o.estado ='TR'") //Tecnico revisando
//            ->orderBy('o.fechaEntrada', 'ASC')
//            ->getQuery()
//            ->getResult()
//            ;
//    }

    public function findTrabajosMostrarJornada($inicio, $fin) //Estos trabajos son los que se ven en la tabla de la jornada del dia en curso
    {                                                          // Todos los trabajos que se han registrado hoy y los de dias anteriores que han quedado pendientes en taller
        return $this->createQueryBuilder('o')
            ->where('o.fechaEntrada <= :fin and o.fechaEntrada >= :inicio')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin)
            ->orWhere("o.fechaEntrada <= :inicio and o.estado IN (:estado)")
            ->setParameter('inicio', $inicio)
            ->setParameter('estado', array('DT','EP','N','LE','RES','ESP', 'ECT'))
            ->orderBy('o.fechaEntrada', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findTrabajosPendientesJornada($inicio, $fin) //PAra cerrar la jornada no pueden existir equipos en el taller que no esten marcados como dejar en el taller
    {
        return $this->createQueryBuilder('o')
            ->where('o.fechaEntrada <= :fin and o.fechaEntrada >= :inicio and o.dejadoEnTaller = false and o.estado IN (:estado)')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin)
            ->setParameter('estado', array('LE', 'RES'))
            //       ->orWhere("o.fechaEntrada < :inicio and o.estado IN (:estado)")
            //  ->setParameter('inicio', $inicio)
            ->orWhere('o.estado IN (:estado_2)')
            ->setParameter('estado_2', array('ESP','ECT','TR'))
            ->orderBy('o.fechaEntrada', 'ASC')
            ->getQuery()->getResult()
            ;
    }

    public function findTrabajosPendientesEnTaller($fin)
    {
        return $this->createQueryBuilder('o')
            ->where('o.fechaEntrada <= :fin and (o.fechaSalida is null or o.fechaSalida > :fin)')
            ->setParameter('fin', $fin)
            ->andWhere("o.estado not in (:estado)")
            ->setParameter('estado', array('CANC','DEC'))
            ->orderBy('o.fechaEntrada', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findReservacionesNoRealizadas(){
        $date = new \DateTime();
        $date->setTime(0,0,0);
        return $this->createQueryBuilder('o')
            ->where('o.fechaEntrada < :date and o.estado in (:estad)')
            ->setParameter('date', $date)
            ->setParameter('estad', array('RES'))
            ->getQuery()
            ->getResult()
            ;
    }

    public function cancelarReservacionesNoRealizadas() : void {
        $date = new \DateTime();
        $date->setTime(0,0,0);
        $q =  $this->createQueryBuilder('o')
            ->update()
            ->set('o.estado', '?1')
            ->setParameter(1, 'CANC')
            ->where('o.fechaEntrada < :date and o.estado in (:estad)')
            ->setParameter('date', $date)
            ->setParameter('estad', array('RES'))
            ->getQuery()
            ;
        $p = $q->execute();
    }

    public function findTodosTrabajosPendientesEnTaller($order = 'ASC')
    {
        $date = new \DateTime();
        $date->setTime(23,59,59);
        return $this->createQueryBuilder('o')
            ->where('o.fechaEntrada < :date and o.fechaSalida is null and o.estado not in (:estad)')
            ->setParameter('date', $date)
            ->setParameter('estad', array('CANC','DEC'))
            ->orderBy('o.fechaEntrada', $order)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findTrabajosPendientesTecnico(Usuario $tecnico)
    {
        return $this->createQueryBuilder('o')
            ->where("o.estado in (:estados)") //En cola para el tecnico
                ->setParameter('estados',array('ECT','TR','EP','DT'))
            //En cola para el tecnico //Tecnico revisando // Se quedo en el taller. En espera de piezas
            // Se quedo en el taller por otros motivos. // Se quedo en el taller por otros motivos.
            ->andWhere('o.tecnicoRepara = :tecnico')
            ->andWhere("o.estado != 'CANC'")
            ->setParameter('tecnico', $tecnico)
            ->orderBy('o.fechaEntrada', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findTrabajosADecomisar($fechaLimite){
        return $this->createQueryBuilder('o')
            ->where('o.fechaEntrada <= :fecha_limite and o.fechaSalida is null')
            ->setParameter('fecha_limite', $fechaLimite)
            ->andWhere("o.estado not in (:estado)")
            ->setParameter('estado', array('CANC','DEC'))
            ->orderBy('o.fechaEntrada', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findSalidasFecha($inicio, $fin) //PAra cerrar la jornada no pueden existir equipos en el taller que no esten marcados como dejar en el taller
    {
        return $this->createQueryBuilder('o')
            ->where('o.fechaSalida <= :fin and o.fechaSalida >= :inicio')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin)
            ->getQuery()->getResult()
            ;
    }

    public function nextNroOrden($garantia, $fecha = null)
    {
        $str = str_replace('/','-',$fecha);
        $fecha = new \DateTime($str);
        $fechaInicio = (clone $fecha)->setTime(0,0,0);
        $fechaFin = (clone $fecha)->setTime(23,59,59);
        try{
        $nro = $this->createQueryBuilder('o')
            ->select('o.nroOrden')
            ->where('o.fechaEntrada >= :inicio and o.fechaEntrada <= :fin')
            ->setParameter('inicio', $fechaInicio)
            ->setParameter('fin', $fechaFin)
            ->orderBy('o.nroOrden','DESC')
            ->setMaxResults(1)
            ->getQuery()->getSingleScalarResult();
        ;} catch (NoResultException $e)
        {
            $nro = null;
        }
        $nuevo = 'OT' . $fecha->format('Ymd') . $this->formarIdTresDigitos((int)substr($nro,10,3)) . ($garantia ? '-G' : '');
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
    //  * @return OrdenTrabajo[] Returns an array of OrdenTrabajo objects
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
    public function findOneBySomeField($value): ?OrdenTrabajo
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
