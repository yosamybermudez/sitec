<?php

namespace App\Repository;

use App\Entity\Evento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Evento|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evento|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evento[]    findAll()
 * @method Evento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evento::class);
    }

    public function findEventosMostrarJornada($inicio, $fin) //Estos trabajos son los que se ven en la tabla de la jornada del dia en curso
    {                                                          // Todos los trabajos que se han registrado hoy y los de dias anteriores que han quedado pendientes en taller
        return $this->createQueryBuilder('e')
            ->where('e.created <= :fin and e.created >= :inicio')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin)
            ->orderBy('e.created', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Evento[] Returns an array of Evento objects
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
    public function findOneBySomeField($value): ?Evento
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
