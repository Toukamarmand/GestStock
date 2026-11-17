<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

        /**
         * @return Commande[] Returns an array of Commande objects
         */
        public function rechercheParPeriode($debut,$fin): array
        {
            return $this->createQueryBuilder('c')
                ->andWhere('DATE(c.date) >= DATE(:debut)')
                ->andWhere('DATE(c.date) <= DATE(:fin)')
                ->setParameter('debut', $debut)
                ->setParameter('fin', $fin)
                ->getQuery()
                ->getResult()
            ;
        }

    public function derniereDate(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.date','ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Commande[] Returns an array of Commande objects
     */
    public function compterparperiode($debut,$fin): array
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as nbrecommande','Date(c.date) as date')
            ->andWhere('DATE(c.date) >= DATE(:debut)')
            ->andWhere('DATE(c.date) <= DATE(:fin)')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->groupBy('date')
            ->getQuery()
            ->getResult()
            ;
    }

    public function commanderecentes(): array
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(l.id) as commande','c.date as date','c.code as code')
            ->innerJoin('c.ligneCommandes','l')
            ->andWhere('c.statut=1')
            ->orderBy('date','DESC')
            ->setMaxResults(5)
            ->groupBy('code')
            ->getQuery()
            ->getResult()
            ;
    }

    public function commandevalider(): array
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as valider','c.code as code')
            ->andWhere('c.statut=1')
            ->getQuery()
            ->getResult()
            ;
    }
    public function commandeNonvalider(): array
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as Nonvalider','c.code as code')
            ->andWhere('c.statut=0')
            ->getQuery()
            ->getResult()
            ;
    }


    //    public function findOneBySomeField($value): ?Commande
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
