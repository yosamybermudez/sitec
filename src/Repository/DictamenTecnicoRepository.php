<?php

namespace App\Repository;

use App\Entity\DictamenTecnico;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DictamenTecnico|null find($id, $lockMode = null, $lockVersion = null)
 * @method DictamenTecnico|null findOneBy(array $criteria, array $orderBy = null)
 * @method DictamenTecnico[]    findAll()
 * @method DictamenTecnico[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DictamenTecnicoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DictamenTecnico::class);
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
    //  * @return DictamenTecnico[] Returns an array of DictamenTecnico objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DictamenTecnico
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
