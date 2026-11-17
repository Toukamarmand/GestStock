<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ImportType;
use App\Form\ProduitType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Services\CommandeService;
use App\Services\ProduitService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//use function Sodium\add;

class ProduitController extends AbstractController
{

    #[Route('/produit/nbreProduitExpirer7Jours', name: 'app_nbreProduitExpirer7Jours')]
    public function nbreProduitExpirer7Jours(CommandeService $commandeService): Response
    {
        $proditExpirer=$commandeService->nbreProduitExpirer7Jours();
        //dd($proditExpirer);
        return $this->render('produit/produitExpirer7Jours.html.twig', [
            'nbreProduitExpirer7Jours'=>$proditExpirer,
            'entite'=>'produit',
            'page'=>'app_produit',

        ]);
    }

    #[Route('/produit/nbreProduitExpirer', name: 'app_nbreProduitExpirer')]
    public function nbreProduitExpirer(CommandeService $commandeService): Response
    {
        $proditExpirer=$commandeService->nbreProduitExpirer();
        //dd($proditExpirer);
        return $this->render('produit/produitExpirer.html.twig', [
            'nbreProduitExpirer'=>$proditExpirer,
            'entite'=>'produit',
            'page'=>'app_produit',

        ]);
    }

    #[Route('/produit/nbreProduitEpuiser', name: 'app_nbreProduitEpuiser')]
    public function produitEpuiser(ProduitService $produitService): Response
    {
        $nbreproduit=$produitService->produitStockEpuiser();
        return $this->render('rapport/produitEpuiser.html.twig', [
            'stockEpuiser'=>$nbreproduit,
            'entite'=>'produit',
            'page'=>'app_produit',

        ]);
    }
    #[Route('/produit', name: 'app_produit')]
    public function index(Request $request, ProduitRepository $produitRepository,
                          CategorieRepository $categorieRepository,ProduitService $produitService): Response
    {
        //verifier et recuperer l'id de la commande
       /* if ($request->get('categorie') !==null){
            $idcategorie=$request->get('categorie');
        }
        else{
            $idcategorie=0;
        }*/
        //verifier et recuperer l'id de la commande une autre maniere
        //$idcategorie=($request->get('categorie')!==null) ? $request->get('categorie') : 0;
        $idcategorie=($request->get('categorie')) ?? 0 ;
        $categories=$categorieRepository->findAll();
        $cat=$categorieRepository->find($idcategorie);
        if ($request->get('produit')==0) {
            $valeur=0;
            $listeproduit = $produitRepository->rechercheParCategorie();
        }
        elseif ($request->get('produit')==1){
            $valeur=1;
            $listeproduit=$produitService->produitStockEpuiserCategorie($cat);
        }
        else{
            $valeur=2;
            $listeproduit=$produitService->produitStockAlerteCategorie($cat);
        }
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $listeproduit,
            'categorie' => $categories,
            'entite'=>'produit',
            'page'=>'app_produit',
            'idcategorie'=>$idcategorie,
            'valeur'=>$valeur,

        ]);
    }

    /*    //Ajout sur avec la methode createFormBuilder
        #[Route('/produit/ajout', name: 'app_produit_ajout')]
        public function ajout(): Response
        {
            //ajout d'un produit
            $form=$this->createFormBuilder()
            ->add('libelle',TextType::class)
            ->add('PrixAchat',NumberType::class)
                ->add('Prixvente',NumberType::class)
                ->add('Quantitealerte',NumberType::class)->getForm();

            return $this->render('produit/ajout.html.twig', [
                'form'=>$form->createView()
            ]);
        }*/

//Ajout sur form
    #[Route('/produit/ajout', name: 'app_produit_ajout')]
    #[IsGranted('ROLE_ADMIN')]
    public function ajout(Request $request, EntityManagerInterface $entityManager): Response
    {
        //ajout d'un produit avec la methode createForm
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class,
            $produit);
        $errors = [];
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $produit->setUser($this->getUser());
                $entityManager->persist($produit);
                $entityManager->flush();
                return $this->redirectToRoute('app_produit');
            } else {
                $errors = $form->getErrors(true);
                $form->clearErrors();
            }
        }

        return $this->render('produit/ajout.html.twig', [
            'errors' => $errors,
            'form' => $form->createView(),
            'entite'=>'produit',
            'page'=>'app_produit_ajout',
        ]);
    }
 /*   #[Route('/produit/ajout', name: 'app_produit_ajout')]
    public function ajout(Request $request, EntityManagerInterface $entityManager): Response
    {
        //ajout d'un produit avec la methode createForm
        $produit = new Produit();
        $form = $this->createForm(ImportType::class,
            $produit);
        $errors = [];
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $excelfile=$form->get('Fichier')->getData();
                $reader= new Xlsx;
                $spreadsheet=$reader->load($excelfile);
                $worksheet=$spreadsheet->getActiveSheet();
                $headerrow=$worksheet->getRowIterator()->current();
                $headercellule=$headerrow->getCellIterator();
                $mapping=[
                    'prix'=>'prix',
                ];
                foreach ($worksheet->getRowIterator(2) as $row){
                    $cellule=$row->getCellIterator();
                    foreach ($cellule as $cell){
                        $column=$cell->getColumn();
                    }
                }
                return $this->redirectToRoute('app_produit');
            } else {
                $errors = $form->getErrors(true);
                $form->clearErrors();
            }
        }

        return $this->render('produit/ajout.html.twig', [
            'errors' => $errors,
            'form' => $form->createView(),
            'entite'=>'produit',
            'page'=>'app_produit_ajout',
        ]);
    }*/

    //Supprimer un produit avec l'id ou un autre champs
 /*   #[Route('/produit/supprimer', name: 'app_produit_supprimer')]
    public function delete(Request $request, EntityManagerInterface $entitymanager, ProduitRepository $produitRepository): Response
    {
        $id=$request->get(('id'));
        $produit=$produitRepository->find($id);
        $entitymanager->remove($produit);
        $entitymanager->flush();
        return $this->redirectToRoute('app_produit');
    }*/
    //supprimer avec l'identifiant slmt
    #[Route('/produit/supprimer/{id}', name: 'app_produit_supprimer')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Produit $produit, EntityManagerInterface $entitymanager, ProduitRepository $produitRepository): Response
    {
        $entitymanager->remove($produit);
        $entitymanager->flush();
        return $this->redirectToRoute('app_produit');
    }

    //EDITER UN PRODUIT
    #[Route('/produit/editer/{id}', name: 'app_produit_editer')]
    public function editer(Produit $produit, Request $request, EntityManagerInterface $entityManager): Response
    {
        //editer d'un produit avec la methode createForm
        $form = $this->createForm(ProduitType::class,
            $produit);
        $errors = [];
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($produit);
                $entityManager->flush();
                return $this->redirectToRoute('app_produit');
            } else {
                $errors = $form->getErrors(true);
                $form->clearErrors();
            }
        }

        return $this->render('produit/ajout.html.twig', [
            'errors' => $errors,
            'form' => $form->createView(),
            'entite'=>'editproduit'
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    #[Route('/produit/import', name: 'app_produit_import')]
    //#[IsGranted('ROLE_ADMIN')]
    public function import(Request $request, EntityManagerInterface $entityManager,
                           CategorieRepository $categorieRepository): Response
    {
        //ajout d'un produit avec la methode createForm
        $form = $this->createForm(ImportType::class);
        $errors = [];
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $excelfile=$form->get('fichier')->getData();
                $spreadsheet=IOFactory::load($excelfile->getpathname());
                $worksheet=$spreadsheet->getActiveSheet();
                $data=[];
                    foreach ($worksheet->getRowIterator(2) as $row) {
                        //$cellule = $row->getColumnIterator();
                        $cellule = $row->getCellIterator();
                    $rowdata = [];
                    //On affecte la valeur de chaque cellule a la ligne (on recupere toute les infos sur le produit)
                    foreach ($cellule as $cell) {
                        $rowdata[] = $cell->getValue();
                        }
                        $data[]=$rowdata;
                      }
                    $i=0;
                    $batchSize=100;
                    //On itere le tableau de produits
                foreach ($data as $rows){
                    $id=$rows[4];
                    //dd($id);
                    $produit=new Produit();
                    $produit->setDesignation($rows[0]);
                    $produit->setPrixAchat($rows[1]);
                    $produit->setPrixVente($rows[2]);
                    $produit->setQteAlerte($rows[3]);
                    $produit->setReference($rows[5]);
                    $categorie=$categorieRepository->find($id);
                    $produit->setCategorie($categorie);
                    $produit->setUser($this->getUser());
                    $entityManager->persist($produit);
                    $i++;
                }
                if ($i%$batchSize===0){
                    $entityManager->flush();
                    $entityManager->clear();
                }
                $entityManager->flush();
                $entityManager->clear();
                return $this->redirectToRoute('app_produit');
                    }
            } else {
                $errors = $form->getErrors(true);
                $form->clearErrors();
            }
        //}

        return $this->render('produit/ajout.html.twig', [
            'errors' => $errors,
            'form' => $form->createView(),
            'entite'=>'produit',
            'page'=>'app_produit_ajout',
        ]);
    }
}
