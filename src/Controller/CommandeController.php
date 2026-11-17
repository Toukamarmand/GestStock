<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Form\LigneCommandeType;
use App\Form\ProduitType;
use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommandeController extends AbstractController
{

    #[Route('/commande', name: 'app_commande')]
    #[IsGranted('ROLE_ADMIN')] //#[IsGranted('ROLE_MAGASINIER')]
    public function ajout(Request $request, ProduitRepository $produitRepository): Response
    {

        $error = [];
        $listeproduit = $produitRepository->findAll();
        $session = $request->getSession();
        $panierCommande = $session->get('panierCommande');
        $error = $session->get('error');
        return $this->render('commande/ajout.html.twig', [
            'produits' => $listeproduit,
            'panierCommande' => $panierCommande,
            'page' => 'app_commande',
            'entite' => 'commande',
            'error' => $error,
        ]);
    }

    #[Route('/commande/liste', name: 'app_commande_liste')]
    #[IsGranted('ROLE_ADMIN')]
    //#[IsGranted('ROLE_MAGASINIER')]
    public function index(Request $request,CommandeRepository $commandeRepository): Response
    {

        $debut=$request->get('debut') ?? new \DateTime();
        $fin=$request->get('fin') ?? new \DateTime();
        $listecommande = $commandeRepository->rechercheParPeriode($debut,$fin);

        return $this->render('commande/index.html.twig',
            [
                'listecommande' => $listecommande,
                'page' => 'app_commande_liste',
                'entite' => 'commande',
                'dateDebut'=>$debut,
                'dateFin'=>$fin,
            ]);
    }

    #[Route('/commande/detail/{id}', name: 'app_commande_detail')]
    #[IsGranted('ROLE_ADMIN')]
    public function detail(Commande $commande, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, CommandeRepository $commandeRepository): Response
    {
        $produit = $produitRepository->findAll();
        //Rechercher la ligne commande par la commande
        $lignecommande = $entityManager->getRepository(LigneCommande::class)->findBy(['commande' => $commande]);
        return $this->render('commande/details.html.twig',
            [
                'listecommande' => $lignecommande,
                'commande' => $commande,
                'produitcommande' => $produit,
                'page' => 'app_commande_liste',
                'entite' => 'commande',
            ]);
    }

    #[Route('/commande/supprimer/{id}', name: 'app_commande_supprimer')]
    public function supprimer(Commande $commande, EntityManagerInterface $entityManager,LigneCommandeRepository $ligneCommandeRepository): Response
    {
        $lignescommande=$commande->getLigneCommandes();
        $entityManager->remove($commande);
        $entityManager->flush();
        return $this->redirectToRoute('app_commande_liste');
    }

    #[Route('/commande/supprimerligne/{id}', name: 'app_commande_supprimerligne')]
    public function supprimerligne(LigneCommandeRepository $ligneCommandeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $ligne = $ligneCommandeRepository->find($request->get('id'));
        $id = $ligne->getCommande()->getId();
        $stock=$ligne->getProduit()->getQtecommande();
        if($stock>0){
            if ($ligne->getQte()<=$stock){
                $entityManager->remove($ligne);
                $entityManager->flush();
                $nbreligne=$ligne->getCommande()->getNombreligne();
                if ($nbreligne==0){
                    return $this->redirectToRoute('app_commande_supprimer', [
                            'id' => $id,
                        ]
                    );
                }
            }
            else{
                $this->addFlash('danger','Le produit ne peut pas etre supprimer car il a été vendu');
            }

        }
        return $this->redirectToRoute('app_commande_detail', [
                'id' => $id,
            ]
        );

    }

    #[Route('/commande/ajoutligne', name: 'app_commande_ajoutligne')]
    #[IsGranted('ROLE_ADMIN')]
  //  #[IsGranted('ROLE_MAGASINIER')]
    public function ajoutligne(Request $request, LigneCommandeRepository $ligneCommandeRepository, CommandeRepository $commandeRepository, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        $commande = $commandeRepository->find($request->get('idcom'));
        $prixu = $request->get('pu');
        $qte = $request->get('qte');
        $prod = $produitRepository->find($request->get('produit'));
        $ligne = $ligneCommandeRepository->find($request->get('idcom'));

        foreach ($commande->getLigneCommandes() as $ligne) {
            if ($prod->getId() === $ligne->getProduit()->getId()) {
                $ligneqte = $ligne->getQte();
                $qtetotal = $qte + $ligneqte;
                $ligne->setQte($qtetotal);
                $ligne->setPrixUnitaire($prixu);
                $ligne->setProduit($prod);
                $ligne->setCommande($commande);
                $entityManager->persist($ligne);
                break;
            }
        }
        if ($prod->getId() !== $ligne->getProduit()->getId()) {
            $ligneCommande = new LigneCommande();
            $ligneCommande->setQte($qte);
            $ligneCommande->setPrixUnitaire($prixu);
            $ligneCommande->setProduit($prod);
            $ligneCommande->setCommande($commande);
            $entityManager->persist($ligneCommande);

        }
        $entityManager->flush();


        return $this->redirectToRoute('app_commande_detail', [
                'id' => $commande->getId(),
            ]
        );

    }


    #[Route('/commande/modifierligne/{id}', name: 'app_commande_modifierligne')]
    #[IsGranted('ROLE_ADMIN')]
  //  #[IsGranted('ROLE_MAGASINIER')]
    public function modifierligne(LigneCommandeRepository $ligneCommandeRepository,
                                  Request                 $request, EntityManagerInterface $entityManager): Response
    {
        $ligne = $ligneCommandeRepository->find($request->get('id'));
        $qte = $request->get('qte');
        $prixu = $request->get('pu');
        /*dd($prixu,$qte,$prod,$commande);*/
        $ligne->setPrixUnitaire($prixu);
        $ligne->setQte($qte);
        $entityManager->persist($ligne);
        $entityManager->flush();

        $id = $ligne->getCommande()->getId();
        return $this->redirectToRoute('app_commande_detail', [
                'id' => $id,
            ]
        );
    }

    #[Route('/commande/valider/{id}', name: 'app_commande_valider')]
    #[IsGranted('ROLE_ADMIN')]
  //  #[IsGranted('ROLE_MAGASINIER')]
    public function valider(CommandeRepository $commandeRepository,
                            Request            $request, EntityManagerInterface $entityManager): Response
    {
        $commande = $commandeRepository->find($request->get('id'));
        $commande->setStatut(true);
        $commande->setUser($this->getUser());
        $entityManager->persist($commande);
        $entityManager->flush();
        $id = $commande->getId();
        return $this->redirectToRoute('app_commande_detail', [
                'id' => $id,
            ]
        );
    }
}
