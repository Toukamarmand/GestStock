<?php

namespace App\Controller;


use App\Dto\ProduitDto;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Services\CommandeService;
use App\Services\PanierCommandeService;
use App\Services\PanierVenteService;
use App\Services\ProduitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AjaxController extends AbstractController
{
    #[Route('/ajax/nbreProduitEpuiser', name: 'app_ajaxNbreProduitEpuiser')]
    public function index(ProduitService $produitService): Response
    {
        $nbreproduit=count($produitService->produitStockEpuiser());
        return $this->json($nbreproduit);
    }
    #[Route('/ajax/nbreProduitExpirer', name: 'app_ajaxNbreProduitExpirer')]
    public function nbreProduitExpirer(CommandeService $commandeService): Response
    {
        $proditExpirer=count($commandeService->nbreProduitExpirer());
        //dd($proditExpirer);
        return $this->json($proditExpirer);
    }
    #[Route('/ajax/nbreProduitExpirer7Jours', name: 'app_ajaxNbreProduitExpirer7Jours')]
    public function nbreProduitExpirer7Jours(CommandeService $commandeService): Response
    {
        $proditExpirer=count($commandeService->nbreProduitExpirer7Jours());
        //dd($proditExpirer);
        return $this->json($proditExpirer);
    }

    #[Route('/ajax/produits', name: 'app_ajaxproduits')]
    public function produit(ProduitRepository $produitRepository): Response
    {
        $produits=$produitRepository->findAll();
        $produitsDto=array_map(function (Produit $produit){
            return new ProduitDto($produit);
        },$produits);
        return $this->json($produitsDto);
    }
    #[Route('/ajax/panier', name: 'app_ajaxpanier')]
    public function panier(Request $request, ProduitRepository $produitRepository): Response
    {
        $session = $request->getSession();
        $panierVente = $session->get('panierVente');

        $data=$this->renderView('Partie/panier.html.twig',[
            'panierVente' => $panierVente,
        ]);
        return $this->json($data);
    }
    #[Route('/ajax/app_ajax_supprimerLigne', name: 'app_ajax_supprimerLigne', methods: ["POST"])]
    public function supprimerLigne(Request $request, PanierVenteService $panierVenteService): Response
    {
        $session = $request->getSession();
        $panierVente = $session->get('panierVente');
        $id = $request->get('id');
        $panierVenteService->supprimerlignepanier($id);
        $data=$this->renderView('Partie/panier.html.twig',[
            'panierVente' => $panierVente,
        ]);
        return $this->json($data);
    }
    #[Route('/ajax/app_ajax_incrementer', name: 'app_ajax_incrementer', methods: ["POST"])]
    public function incrementer(Request $request, PanierVenteService $panierVenteService): Response
    {
        $session = $request->getSession();
        $panierVente = $session->get('panierVente');
        $id = $request->get('id');
        $panierVenteService->increment($id);
        $data=$this->renderView('Partie/panier.html.twig',[
            'panierVente' => $panierVente,
        ]);
        return $this->json($data);
    }

    #[Route('/ajax/app_ajax_decrementer', name: 'app_ajax_decrementer', methods: ["POST"])]
    public function decrementer(Request $request, PanierVenteService $panierVenteService): Response
    {
        $session = $request->getSession();
        $panierVente = $session->get('panierVente');
        $id = $request->get('id');
        $panierVenteService->decrementer($id);
        $data=$this->renderView('Partie/panier.html.twig',[
            'panierVente' => $panierVente,
        ]);
        return $this->json($data);
    }
    #[Route('/ajax/app_ajax_modifierqte', name: 'app_ajax_modifierqte', methods: ["POST"])]
    public function modifierqte(Request $request, PanierVenteService $panierVenteService): Response
    {
        $session = $request->getSession();
        $panierVente = $session->get('panierVente');
        $id = $request->get('id');
        $qte=$request->get('qte');
        $panierVenteService->modifier($id,$qte);
        $data=$this->renderView('Partie/panier.html.twig',[
            'panierVente' => $panierVente,
        ]);
        return $this->json($data);
    }
    #[Route('/ajax/panierCommande', name: 'app_ajax_panierCommande')]
    public function panierCommande(Request $request, ProduitRepository $produitRepository): Response
    {
        $session = $request->getSession();
        $panierCommande = $session->get('panierCommande');

        $data=$this->renderView('Partie/panierCommande.html.twig',[
            'panierCommande' => $panierCommande,
        ]);
        return $this->json($data);
    }
    #[Route('/ajax/app_ajax_supprimerLigneCommande', name: 'app_ajax_supprimerLigneCommande', methods: ["POST"])]
    public function supprimerLigneCommande(Request $request, PanierCommandeService $panierCommandeService): Response
    {
        $session = $request->getSession();
        $panierCommande = $session->get('panierCommande');
        $id = $request->get('id');
        $panierCommandeService->supprimerlignepanier($id);
        $data=$this->renderView('Partie/panierCommande.html.twig',[
            'panierCommande' => $panierCommande,
        ]);
        return $this->json($data);
    }
    #[Route('/ajax/app_ajax_incrementerCommande', name: 'app_ajax_incrementerCommande', methods: ["POST"])]
    public function incrementerCommande(Request $request, PanierCommandeService $panierCommandeService): Response
    {
        $session = $request->getSession();
        $panierCommande = $session->get('panierCommande');
        $id = $request->get('id');
        $panierCommandeService->increment($id);
        $data=$this->renderView('Partie/panierCommande.html.twig',[
            'panierCommande' => $panierCommande,
        ]);
        return $this->json($data);
    }
    #[Route('/ajax/app_ajax_decrementerCommande', name: 'app_ajax_decrementerCommande', methods: ["POST"])]
    public function decrementerCommande(Request $request, PanierCommandeService $panierCommandeService): Response
    {
        $session = $request->getSession();
        $panierCommande = $session->get('panierCommande');
        $id = $request->get('id');
        $panierCommandeService->decrementer($id);
        $data=$this->renderView('Partie/panierCommande.html.twig',[
            'panierCommande' => $panierCommande,
        ]);
        return $this->json($data);
    }
    #[Route('/ajax/app_ajax_modifierqteCommande', name: 'app_ajax_modifierqteCommande', methods: ["POST"])]
    public function modifierqteCommande(Request $request, PanierCommandeService $panierCommandeService): Response
    {
        $session = $request->getSession();
        $panierCommande = $session->get('panierCommande');
        $id = $request->get('id');
        $qte=$request->get('qte');
        $panierCommandeService->modifier($id,$qte);
        $data=$this->renderView('Partie/panierCommande.html.twig',[
            'panierCommande' => $panierCommande,
        ]);
        return $this->json($data);
    }
    #[Route('/ajax/app_ajax_modifierdateCommande', name: 'app_ajax_modifierdateCommande', methods: ["POST"])]
    public function modifierdateCommande(Request $request, PanierCommandeService $panierCommandeService): Response
    {
        $session = $request->getSession();
        $panierCommande = $session->get('panierCommande');
        $id = $request->get('id');
        $date=$request->get('date');
        $panierCommandeService->modifierdate($id,$date);
        $data=$this->renderView('Partie/panierCommande.html.twig',[
            'panierCommande' => $panierCommande,
        ]);
        return $this->json($data);
    }
}
