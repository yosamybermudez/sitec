<?php

namespace App\Repository;

use App\Entity\Jornada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jornada|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jornada|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jornada[]    findAll()
 * @method Jornada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JornadaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jornada::class);
    }

    public function findByDate($fecha)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.fecha = :fecha')
            ->setParameter('fecha', $fecha)
//            ->andWhere('o.fecha >= :inicio')
//            ->setParameter('inicio', $inicio)
            ->orderBy('o.fecha', 'ASC')
            ->getQuery()
            ->getSingleResult()
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

    // /**
    //  * @return Jornada[] Returns an array of Jornada objects
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
    public function findOneBySomeField($value): ?Jornada
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
