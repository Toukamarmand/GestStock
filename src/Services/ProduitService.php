<?php

namespace App\Services;

use App\Entity\LigneVente;
use App\Entity\Produit;
use App\Entity\Vente;
use App\Panier\ProduitCard;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use phpDocumentor\Reflection\Types\Static_;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class ProduitService
{
    private ProduitRepository $produitRepository;

    public function __construct( ProduitRepository $produitRepository){
        $this->produitRepository=$produitRepository;
    }/*
        public function produitStockEpuiser(){
        $produits=$this->produitRepository->findAll();
        $produitsfilter= array_filter($produits, static function (Produit $produit){
            return ($produit->getQtecommande()<=0);
        }
        );
            return array_map(static function (Produit $produit){
                return [$produit->getDesignation(),$produit->getQtecommande()] ;
            },$produitsfilter);
        }*/

    public function produitStockEpuiser(){
        $produits=$this->produitRepository->findAll();
        return array_filter($produits, static function (Produit $produit){
            return ($produit->getQtecommande()<=0);
        }
        );

    }
    public function produitStockEpuiserCategorie($cat){
        $produits=$this->produitRepository->rechercheParCategorie($cat);
        return array_filter($produits, static function (Produit $produit) use ($cat){
            return ($produit->getQtecommande()<=0);
        }
        );
    }
    public function produitStockAlerteCategorie($cat){
        $produits=$this->produitRepository->rechercheParCategorie($cat);
        return array_filter($produits, static function (Produit $produit) use ($cat){
            return ($produit->getQtecommande()<=$produit->getQteAlerte());
        }
        );
    }

}