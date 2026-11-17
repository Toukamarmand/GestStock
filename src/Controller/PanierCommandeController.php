<?php

namespace App\Controller;


use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Panier\ProduitCard;
use App\Repository\ProduitRepository;
use App\Services\PanierCommandeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Sodium\add;

class PanierCommandeController extends AbstractController
{
    #[Route('/panierCommande/increment/{id}', name: 'app_panierCommande_increment')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_MAGASINIER')]
    public function index(PanierCommandeService $panierCommandeService, Request $request): Response
    {
        $panierCommandeService->increment($request->get('id'));
        /*     //On recherche le produit par son id
                  $produit=$produitRepository->find($request->get('id'));
                  //On recupere l'objet de session
                  $session=$request->getSession();
                  //On recupere la variable de panierCommande dans la session active
                  $pannierCommande=$session->get('panierCommande',[]);


        if(empty(($pannierCommande))){
              $lignePanier=new ProduitCard();
              $lignePanier->setProduit($produit);
              $lignePanier->setPrixUnitaire($produit->getPrixAchat());
              $pannierCommande[]=$lignePanier;
          }else{
              //1-ON ITERE SUR CHAQUE LIGNE DU PANIER
              foreach ($pannierCommande as $key=>$lignePanier){
                  //2-On compare l'identifiant du produit de la ligne en cours avec
                  // l'identifiant du produit que l'on veut ajouter
                  if($lignePanier->getProduit()->getId()==$produit->getId()){
                      //On met a jour la ligne au cas ou les identifiants correspondent
                      $lignePanier->setQte($lignePanier->getQte()+1);
                      $pannierCommande[$key]=$lignePanier;
                      break;
                  }
              }

              //On ajoute une nouvelle ligne si on a pas trouve de correspondance
              if($lignePanier->getProduit()->getId()!==$produit->getId()){
                  $lignePanier=new ProduitCard();
                  $lignePanier->setProduit($produit);
                  $lignePanier->setPrixUnitaire($produit->getPrixAchat());
                  $pannierCommande[]=$lignePanier;
              }
          }
                  $session->set('panierCommande',$pannierCommande);
   */
                return $this->redirectToRoute('app_commande');
            }

    #[Route('/panierCommande/decrement', name: 'app_panierCommande_decrement')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_MAGASINIER')]
    public function decrement(PanierCommandeService $panierCommandeService, Request $request): Response
    {
        $panierCommandeService->decrementer($request->get('id'));
       /* $error=[];
        //On recherche le produit par son id
        $produit=$produitRepository->find($request->get('id'));
        //On recupere l'objet de session
        $session=$request->getSession();
        //On recupere la variable de panierCommande dans la session active
        $pannierCommande=$session->get('panierCommande',[]);

        if(empty(($pannierCommande))){
            $this->addFlash('danger','Le panier est vide');
        }
        else{
            //1-ON ITERE SUR CHAQUE LIGNE DU PANIER
            foreach ($pannierCommande as $key=>$lignePanier){
                //2-On compare l'identifiant du produit de la ligne en cours avec
                // l'identifiant du produit que l'on veut ajouter
                if($lignePanier->getProduit()->getId()==$produit->getId()){
                    //On met a jour la ligne au cas ou les identifiants correspondent
                    $qte=$lignePanier->getQte();
                    if($qte<=1){
                        $this->addFlash('danger','La quantite doit etre superieur a 1');
                    }
                    else{
                        $lignePanier->setQte($lignePanier->getQte()-1);
                    }
                    $pannierCommande[$key]=$lignePanier;
                    break;
                }
            }

      /*      //On ajoute une nouvelle ligne si on a pas trouve de correspondance
            if($lignePanier->getProduit()->getId()!==$produit->getId()){
                $lignePanier=new ProduitCard();
                $lignePanier->setProduit($produit);
                $lignePanier->setPrixUnitaire($produit->getPrixAchat());
                $pannierCommande[]=$lignePanier;
            }
        }

        //$id=$request->get('id');

        $session->set('panierCommande',$pannierCommande);
        $session->set('error',$error);*/
        return $this->redirectToRoute('app_commande');
    }


    #[Route('/panierCommande/supprimer/{id}', name: 'app_panierCommande_supprimer')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_MAGASINIER')]
    public function suppress(Request $request,PanierCommandeService $panierCommandeService): Response
    {
        $panierCommandeService->supprimerlignepanier($request->get('id'));
       /* $produit=$produitRepository->find($request->get('id'));
        //On recupere l'objet de session
        $session=$request->getSession();
        //On recupere la variable de panierCommande dans la session active
        $pannierCommande=$session->get('panierCommande',[]);

            //1-Parcourir le tableau pour trouver et supprimer l'element
            foreach ($pannierCommande as $key=>$lignePanier) {
                //2-On compare l'identifiant du produit de la ligne en cours avec
                // l'identifiant du produit que l'on veut ajouter
                if ($lignePanier->getProduit()->getId() === $produit->getId()) {
                    //On met a jour la ligne au cas ou les identifiants correspondent
                    unset($pannierCommande[$key]);
                    //Supprimer l'element
                    break;
                }
            }
            //Reindexer le tableau pour eviter les trous dans les cles
        $pannierCommande=array_values($pannierCommande);

        $session->set('panierCommande',$pannierCommande);
        $this->addFlash('success',"Le produit ".$produit->getDesignation()." a été supprimé");*/

        return $this->redirectToRoute('app_commande');
    }

    #[Route('/panierCommande/delete', name: 'app_panierCommande_delete')]
    public function delete(Request $request, PanierCommandeService $panierCommandeService): Response
    {
        $panierCommandeService->viderpanier();
        return $this->redirectToRoute('app_commande');
    }


    #[Route('/panierCommande/valider', name: 'app_panierCommande_valider')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_MAGASINIER')]
    public function valider(PanierCommandeService $panierCommandeService): Response
    {
        $panierCommandeService->ValiderPanier();
        /*$session=$request->getSession();
        $pannierCommande=$session->get('panierCommande',[]);
        if(empty(($pannierCommande))){
            $this->addFlash('danger','Le panier est vide');
        }
        else{
            $commande=new Commande();
            $code=random_int('5','100');
            $date=new \DateTime();
            $commande->setCode($code);
            $commande->setDate($date);
            $commande->setStatut(false);
            $entityManager->persist($commande);

            foreach ($pannierCommande as $lignePanier){
                $ligne=new LigneCommande();
                $produit=$produitRepository->find($lignePanier->getProduit()->getId());
                    $ligne->setPrixUnitaire($lignePanier->getprixUnitaire());
                    $ligne->setQte($lignePanier->getqte());
                    $ligne->setProduit($produit);
                    $ligne->setCommande($commande);
                    $entityManager->persist($ligne);
            }
            $entityManager->flush();
            /*$pannierCommande=[];

        }
       $session->set('panierCommande',[]);*/
        return $this->redirectToRoute('app_commande');
    }



}
