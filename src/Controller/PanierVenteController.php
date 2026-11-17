<?php

namespace App\Controller;


use App\Entity\Produit;
use App\Entity\Vente;
use App\Entity\LigneVente;
use App\Panier\ProduitCard;
use App\Repository\ProduitRepository;
use App\Services\PanierVenteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
//use function Sodium\add;

class PanierVenteController extends AbstractController
{
    #[Route('/panierVente/increment/{id}', name: 'app_panierVente_increment')]
    #[IsGranted('ROLE_ADMIN')]
    //#[IsGranted('ROLE_VENDEUR')]
    public function index(PanierVenteService $panierVenteService, Request $request): Response
    {
        //On recherche le produit par son id
        $panierVenteService->increment($request->get('id'));

        /* //On recupere l'objet de session
        $session = $request->getSession();
        //On recupere la variable de panierVente dans la session active
        $pannierVente = $session->get('panierVente', []);


        if($produit->getQtecommande()==0){
            $this->addFlash('danger','La quantite doit etre superieur a 0');
        }
        else{
            if (empty(($pannierVente))) {
                $lignePanier = new ProduitCard();
                $lignePanier->setProduit($produit);
                $lignePanier->setPrixUnitaire($produit->getPrixVente());
                $pannierVente[] = $lignePanier;
            } else {
                //1-ON ITERE SUR CHAQUE LIGNE DU PANIER
                foreach ($pannierVente as $key => $lignePanier) {
                    //2-On compare l'identifiant du produit de la ligne en cours avec
                    // l'identifiant du produit que l'on veut ajouter
                    if ($lignePanier->getProduit()->getId() == $produit->getId()) {
                        if ($produit->getQtecommande()<=$lignePanier->getQte()){
                            $this->addFlash('danger','La quantite en stock doit etre inferieure a la quantite du panier');
                        }
                        else {
                            //On met a jour la ligne au cas ou les identifiants correspondent
                            $lignePanier->setQte($lignePanier->getQte() + 1);
                        }
                        $pannierVente[$key] = $lignePanier;
                        break;
                    }
                }

                //On ajoute une nouvelle ligne si on a pas trouve de correspondance
                if ($lignePanier->getProduit()->getId() !== $produit->getId()) {
                    $lignePanier = new ProduitCard();
                    $lignePanier->setProduit($produit);
                    $lignePanier->setPrixUnitaire($produit->getPrixVente());
                    $pannierVente[] = $lignePanier;
                }
            }
            //$id=$request->get('id');
            $session->set('panierVente', $pannierVente);
        }*/

        return $this->redirectToRoute('app_vente');
    }

    #[Route('/panierVente/decrement', name: 'app_panierVente_decrement')]
    #[IsGranted('ROLE_ADMIN')]
    //#[IsGranted('ROLE_VENDEUR')]
    public function decrement(PanierVenteService $panierVenteService,Request $request): Response
    {
        $panierVenteService->decrementer($request->get('id'));
        /*$error = [];
        //On recherche le produit par son id
        $produit = $produitRepository->find($request->get('id'));
        //On recupere l'objet de session
        $session = $request->getSession();
        //On recupere la variable de panierVente dans la session active
        $pannierVente = $session->get('panierVente', []);

        if (empty(($pannierVente))) {
            $this->addFlash('danger', 'Le panier est vide');
        } else {
            //1-ON ITERE SUR CHAQUE LIGNE DU PANIER
            foreach ($pannierVente as $key => $lignePanier) {
                //2-On compare l'identifiant du produit de la ligne en cours avec
                // l'identifiant du produit que l'on veut ajouter
                if ($lignePanier->getProduit()->getId() == $produit->getId()) {
                    //On met a jour la ligne au cas ou les identifiants correspondent
                    $qte = $lignePanier->getQte();
                    if ($qte <= 1) {
                        $this->addFlash('danger', 'La quantite doit etre superieur a 1');
                    } else {
                        $lignePanier->setQte($lignePanier->getQte() - 1);
                    }
                    $pannierVente[$key] = $lignePanier;
                    break;
                }
            }

            //On ajoute une nouvelle ligne si on a pas trouve de correspondance
            if ($lignePanier->getProduit()->getId() !== $produit->getId()) {
                $lignePanier = new ProduitCard();
                $lignePanier->setProduit($produit);
                $lignePanier->setPrixUnitaire($produit->getPrixVente());
                $pannierVente[] = $lignePanier;
            }
        }

        //$id=$request->get('id');

        $session->set('panierVente', $pannierVente);
        $session->set('error', $error);*/
        return $this->redirectToRoute('app_vente');
    }


    #[Route('/panierVente/supprimer/{id}', name: 'app_panierVente_supprimer')]
    #[IsGranted('ROLE_ADMIN')]
    //#[IsGranted('ROLE_VENDEUR')]
    public function suppress(Request $request, PanierVenteService $panierVenteService): Response
    {
        $panierVenteService->supprimerlignepanier($request->get('id'));
       /* $produit = $produitRepository->find($request->get('id'));
        //On recupere l'objet de session
        $session = $request->getSession();
        //On recupere la variable de panierVente dans la session active
        $pannierVente = $session->get('panierVente', []);

        //1-Parcourir le tableau pour trouver et supprimer l'element
        foreach ($pannierVente as $key => $lignePanier) {
            //2-On compare l'identifiant du produit de la ligne en cours avec
            // l'identifiant du produit que l'on veut ajouter
            if ($lignePanier->getProduit()->getId() === $produit->getId()) {
                //On met a jour la ligne au cas ou les identifiants correspondent
                unset($pannierVente[$key]);
                //Supprimer l'element
                break;
            }
        }
        //Reindexer le tableau pour eviter les trous dans les cles
        $pannierVente = array_values($pannierVente);

        $session->set('panierVente', $pannierVente);
        $this->addFlash('success', "Le produit " . $produit->getDesignation() . " a été supprimé");*/

        return $this->redirectToRoute('app_vente');
    }

    #[Route('/panierVente/delete', name: 'app_panierVente_delete')]
    #[IsGranted('ROLE_ADMIN')]
    //#[IsGranted('ROLE_VENDEUR')]
    public function delete(PanierVenteService $panierVenteService): Response
    {
        $panierVenteService->viderpanier();
        /*$session = $request->getSession();
        $produit = $request->get('id');
        $pannierVente = [];
        $session->set('panierVente', $pannierVente);
        $this->addFlash('success', 'Le panier est vide');*/
        return $this->redirectToRoute('app_vente');
    }


    #[Route('/panierVente/valider', name: 'app_panierVente_valider')]
    #[IsGranted('ROLE_ADMIN')]
    //#[IsGranted('ROLE_VENDEUR')]
    public function valider(PanierVenteService $panierVenteService): Response
    {
        $panierVenteService->ValiderPanier();
        /*    $session = $request->getSession();
          $pannierVente = $session->get('panierVente', []);
        if (empty(($pannierVente))) {
              $this->addFlash('danger', 'Le panier est vide');
          } else {
              $vente = new Vente();
              $code = random_int('5', '100');
              $date = new \DateTime();
              $vente->setCode($code);
              $vente->setDate($date);
              $entityManager->persist($vente);

              foreach ($pannierVente as $lignePanier) {
                  $ligne = new LigneVente();
                  $produit = $produitRepository->find($lignePanier->getProduit()->getId());
                  $ligne->setPrixUnitaire($lignePanier->getprixUnitaire());
                  $ligne->setQte($lignePanier->getqte());
                  $ligne->setProduit($produit);
                  $ligne->setVente($vente);
                  $entityManager->persist($ligne);
              }
              $entityManager->flush();


          }
          $session->set('panierVente', []);*/
        return $this->redirectToRoute('app_vente');
    }


}
