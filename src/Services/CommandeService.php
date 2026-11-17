<?php

namespace App\Services;

use App\Entity\LigneCommande;
use App\Entity\LigneVente;
use App\Entity\Produit;
use App\Entity\Vente;
use App\Panier\ProduitCard;
use App\Repository\LigneCommandeRepository;
use App\Repository\ProduitRepository;
use App\Utile\Gestiondate;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use phpDocumentor\Reflection\Types\Static_;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class CommandeService
{
    private LigneCommandeRepository $ligneCommandeRepository;
    private Gestiondate $gestiondate;

    /**
     * @param LigneCommandeRepository $ligneCommandeRepository
     * @param Gestiondate $gestiondate
     */
    public function __construct(LigneCommandeRepository $ligneCommandeRepository, Gestiondate $gestiondate)
    {
        $this->ligneCommandeRepository = $ligneCommandeRepository;
        $this->gestiondate = $gestiondate;
    }


    public function nbreProduitExpirer($com=null){
        $dateActuelle=new \DateTime();
        //= \DateTime::createFromFormat('Y-m-d',$dat);
        //$date=$this->gestiondate->soustrairesemaine();
        if ($com==null){
            $lignes=$this->ligneCommandeRepository->findAll();
            return array_filter($lignes, static function (LigneCommande $ligne) use ($dateActuelle){
                return ($ligne->getDateExpiration()<$dateActuelle);
            }
            );
        }
    }
    public function nbreProduitExpirer7Jours($com=null){
        $dateActuelle=new \DateTime();
        //= \DateTime::createFromFormat('Y-m-d',$dat);
        $date=$this->gestiondate->ajoutsemaine();
        if ($com==null){
            $lignes=$this->ligneCommandeRepository->findAll();
            return array_filter($lignes, static function (LigneCommande $ligne) use ($date,$dateActuelle){
                return ($ligne->getDateExpiration()>=$dateActuelle  && $ligne->getDateExpiration()<$date);
            }
            );
        }
    }

}