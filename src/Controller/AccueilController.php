<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\VenteRepository;
use App\Utile\Gestiondate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(Request $request, VenteRepository $venteRepository, ProduitRepository $produitRepository, CommandeRepository $commandeRepository,Gestiondate $gestiondate): Response
    {
        $fin=new \DateTime();
        $iddate=$request->get('id');
        if($iddate==1){
            $debut=$gestiondate->soustrairemois();
        }elseif ($iddate==2){
            $debut=$gestiondate->soustraireannees();
        }
        elseif ($iddate==0){
            $debut=$fin;
        }
        else{
            $debut=$commandeRepository->derniereDate();
        }
        //$arr=$commandeRepository->derniereDate();
        //dd($iddate);
        $nbreproduit=$produitRepository->count();
        $nbrecommande=$commandeRepository->count();
        $nbrevente=$venteRepository->count();
        $nbrecommandepardate=$commandeRepository->compterparperiode($debut,$fin);
        $produitParCatgorie=$produitRepository->produitParCategorie();
        $produitrecent=$produitRepository->produitrecent();
        $commanderecente=$commandeRepository->commanderecentes();
        $commandevalider=$commandeRepository->commandevalider();
        $commandeNonvalider=$commandeRepository->commandeNonvalider();
        $produits=$produitRepository->findAll();
        $profit=0;
        foreach ($produits as $produit){
            $quantiteCommander=$produit->getQtecommander();
            $montant=($produit->getPrixVente()-$produit->getPrixAchat())* $quantiteCommander;
            $profit+=$montant;
        }
        //dd($profit);
        return $this->render('accueil/index.html.twig', [
            'entite'=>'accueil',
            'page'=>'app_accueil',
            'nbreproduit'=>$nbreproduit,
            'commande'=>$nbrecommande,
            'vente'=>$nbrevente,
            'commandepardate'=>$nbrecommandepardate,
            'produitParCategorie'=>$produitParCatgorie,
            'produitrecent'=>$produitrecent,
            'valider'=>$commandevalider,
            'Nonvalider'=>$commandeNonvalider,
            'commanderecente'=>$commanderecente,
            'profit'=>$profit,

        ]);
    }
}
