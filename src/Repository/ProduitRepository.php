<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

//        /**
//         * @return Produit[] Returns an array of Produit objects
//         */
//        public function rechercheProduitLibelle($value=null,$max=10): array
//        {
//            $qd= $this->createQueryBuilder('p')
//                ->select("p.designation","p.prixAchat");
//            if ($value!==null){
//                $qd->where('p.prixAchat > :prix_achat')
//                    ->setParameter('prix_achat', $value);
//            }
//            $qd->orderBy('p.id', 'ASC')
//            ->setMaxResults($max);
//            return $qd->getQuery()
//                ->getResult()
//            ;
//        }


    /**
     * @return Produit[] Returns an array of Produit objects
     */
    public function rechercheParCategorie($value=null): array
    {
        $qd= $this->createQueryBuilder('p')
            ->innerJoin("p.categorie","c");
        if ($value!==null){
            $qd->where('c = :categorie')
                ->setParameter('categorie', $value);
        }
        return $qd->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function produitParCategorie(): array
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as nbProduit','c.libelle as libel')
            ->innerJoin("p.categorie","c")
            ->groupBy('c.libelle')
            ->getQuery()
            ->getResult()
            ;
    }

    public function produitrecent(): array
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(l.qte) as vente','p.designation as designation','p.prixVente as PV','c.libelle as libelle')
            ->innerJoin("p.categorie","c")
            ->innerJoin('p.ligneVentes','l')
            ->orderBy('vente','DESC')
            ->setMaxResults(5)
            ->groupBy('p.id')
            ->getQuery()
            ->getResult()
            ;
    }
    public function profit(): array
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.prixVente-p.prixAchat) as profit')
            ->getQuery()
            ->getResult()
            ;
    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
