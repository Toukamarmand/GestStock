<?php

namespace App\Controller;

use App\Entity\LigneVente;
use App\Entity\Vente;
use App\Entity\Produit;
use App\Repository\VenteRepository;
use App\Repository\LigneVenteRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VenteController extends AbstractController
{
    #[Route('/vente', name: 'app_vente')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_VENDEUR')]
    public function ajout(Request $request, ProduitRepository $produitRepository): Response
    {

        $listeproduit = $produitRepository->findAll();

        $session = $request->getSession();
        //$panierVente = $session->get('panierVente');
        $error = $session->get('error');
        $page='app_vente';
        return $this->render('vente/ajout.html.twig', [
            'controller_name' => 'VenteController',
            'produits' => $listeproduit,
            //'panierVente' => $panierVente,
            'page'=>$page,
            'entite'=>'vente',
            'stock'=>$error,
        ]);
    }

    #[Route('/vente/liste', name: 'app_vente_liste')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_VENDEUR')]
    public function index(VenteRepository $venteRepository): Response
    {
        $listevente = $venteRepository->findAll();
        return $this->render('vente/index.html.twig',
            [
                'listevente' => $listevente,
                'page' => 'app_vente_liste',
                'entite' => 'vente',
            ]);
    }
    #[Route('/facture/print{id}', name: 'app_facture_print')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_VENDEUR')]
    public function print(Vente $vente, EntityManagerInterface $entityManager, Request $request, VenteRepository $venteRepository,Pdf $pdf): PdfResponse
    {
        $pdf->setOption('enable-local-file-access',true);
        //Pour modifier la hauteur
        $pdf->setOption('page-height',210);
        //Largeur de la page
        $pdf->setOption('page-width',80);
        $pdf->setOption('margin-left',5);
        $pdf->setOption('margin-right',5);
        $pdf->setOption('margin-top',2);
            //Rechercher la ligne vente par la vente
            $lignevente = $entityManager->getRepository(LigneVente::class)->findBy(['vente' => $vente]);
            $route=$this->renderView('pdf/facture.html.twig',[
                'listevente' => $lignevente,
                'vente' => $vente,
            ]);
            return new PdfResponse(
                $pdf->getOutputFromHtml($route),
                'facture.pdf',
                'application/pdf',
                'inline'
            );
        }
    
    #[Route('/vente/supprimer/{id}', name: 'app_vente_supprimer')]
    #[IsGranted('ROLE_ADMIN')]
    public function supprimer(Vente $vente, EntityManagerInterface $entityManager): Response
    {
        $lignesvente=$vente->getLigneVentes();
        $entityManager->remove($vente);
        $entityManager->flush();
        return $this->redirectToRoute('app_vente_liste');
    }

    #[Route('/vente/detail/{id}', name: 'app_vente_detail')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_VENDEUR')]
    public function detail(Vente $vente, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, VenteRepository $venteRepository): Response
    {
        $produit = $produitRepository->findAll();
        //Rechercher la ligne vente par la vente
        $lignevente = $entityManager->getRepository(LigneVente::class)->findBy(['vente' => $vente]);
        return $this->render('vente/details.html.twig',
            [
                'listevente' => $lignevente,
                'vente' => $vente,
                'produitvente' => $produit,
                'page' => 'app_vente_liste',
                'entite' => 'vente',
            ]);
    }
    #[Route('/vente/supprimerligne/{id}', name: 'app_vente_supprimerligne')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_VENDEUR')]
    public function supprimerligne(LigneVenteRepository $ligneVenteRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $ligne = $ligneVenteRepository->find($request->get('id'));
        $id = $ligne->getVente()->getId();
        $entityManager->remove($ligne);
        $entityManager->flush();
        $nbreligne=$ligne->getVente()->getNombreligne();
        if ($nbreligne==0){
            return $this->redirectToRoute('app_vente_supprimer', [
                    'id' => $id,
                ]
            );
        }else{

            return $this->redirectToRoute('app_vente_detail', [
                    'id' => $id,
                ]
            );
        }

    }

    #[Route('/vente/ajoutligne', name: 'app_vente_ajoutligne')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_VENDEUR')]
    public function ajoutligne(Request $request, LigneVenteRepository $ligneVenteRepository, VenteRepository $venteRepository, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        $vente = $venteRepository->find($request->get('idcom'));
        $prixu = $request->get('pu');
        $qte = $request->get('qte');
        $prod = $produitRepository->find($request->get('produit'));
        $ligne = $ligneVenteRepository->find($request->get('idcom'));

        foreach ($vente->getLigneVentes() as $ligne) {
            if ($prod->getId() === $ligne->getProduit()->getId()) {
                $ligneqte = $ligne->getQte();
                $qtetotal = $qte + $ligneqte;
                $ligne->setQte($qtetotal);
                $ligne->setPrixUnitaire($prixu);
                $ligne->setProduit($prod);
                $ligne->setVente($vente);
                $entityManager->persist($ligne);
                break;
            }
        }
        if ($prod->getId() !== $ligne->getProduit()->getId()) {
            $ligneVente = new LigneVente();
            $ligneVente->setQte($qte);
            $ligneVente->setPrixUnitaire($prixu);
            $ligneVente->setProduit($prod);
            $ligneVente->setVente($vente);
            $entityManager->persist($ligneVente);

        }
        $entityManager->flush();


        return $this->redirectToRoute('app_vente_detail', [
                'id' => $vente->getId(),
            ]
        );

    }
    
    #[Route('/vente/modifierligne/{id}', name: 'app_vente_modifierligne')]
    #[IsGranted('ROLE_ADMIN')]
   // #[IsGranted('ROLE_VENDEUR')]
    public function modifierligne(LigneVenteRepository $ligneVenteRepository,
                                  Request                 $request, EntityManagerInterface $entityManager): Response
    {
        $ligne = $ligneVenteRepository->find($request->get('id'));
        $qte = $request->get('qte');
        $prixu = $request->get('pu');
        /*dd($prixu,$qte,$prod,$vente);*/
        $ligne->setPrixUnitaire($prixu);
        $ligne->setQte($qte);
        $entityManager->persist($ligne);
        $entityManager->flush();

        $id = $ligne->getVente()->getId();
        return $this->redirectToRoute('app_vente_detail', [
                'id' => $id,
            ]
        );
    }
}
