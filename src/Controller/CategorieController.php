<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use PharIo\Manifest\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategorieController extends AbstractController
{
    #[Route('/categories', name: 'app_categorie')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $listcategorie = $categorieRepository->findAll();

        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
            'categories' => $listcategorie,
            'page'=>'app_categorie',
            'entite'=>'categorie',
        ]);
    }
/*
    //code pour ajouter une categorie
    #[Route('/categorie/ajout', name: 'app_categorie_ajout')]
    public function ajout(Request $request, EntityManagerInterface $entitymanager): Response
    {
        $form = $this->createFormBuilder(null, [
            'attr' => [
                'class' => ' p-4'
            ]
        ])
            ->add('libelle', TextType::class,
                //Pour styliser l'input du libelle
                [
                    //'required'=>false,
                    'attr' => [
                        'class' => '',
                        'placeholder' => 'Entrer le libelle de la categorie'
                    ],
                    'constraints' => [

                        new Length(null, 3, 50),
                        new NotBlank(null,"Le champ ne doit pas etre vide")
                    ],
                    'error_bubbling'=>true,
                ])->getForm();
        //Si la methode est post
        $errors=[];
        if ($request->isMethod('POST')) {
            //On arrete la requete ($request) du formulaire
            $form->handleRequest($request);
            //Verifier si le formulaire a ete soumis et verifier la validite de tous les champs
            if ($form->isSubmitted() && $form->isValid()) {
                //Recuperer les donnees du formulaire
                $data = $form->getData();
                //dd($data);

                $categorie=new Categorie();

                $categorie->setlibelle($data['libelle']);

                $entitymanager->persist($categorie);

                $entitymanager->flush();

                //La methode propre au controller pour faire la redirection est
                return $this->redirectToRoute('app_categorie');

            }else{
                $errors=$form->getErrors(true);
                $form->clearErrors();
                //dd($errors);
            }
        }
        return $this->render('categorie/ajout.html.twig', [
            'form' => $form->createView(),
            'errors'=>$errors,
        ]);*/
   // }
    //fin code Ajout

    #[Route('/categorie/ajout', name: 'app_categorie_ajout')]
    #[IsGranted('ROLE_ADMIN')]
    public function ajout(Request $request, EntityManagerInterface $entitymanager): Response
    {
        $page='app_categorie_ajout';
        $categorie=new Categorie();
        $form=$this->createForm(CategorieType::class,
        $categorie);
        $errors=[];
        if ($request->isMethod('POST')) {
            //On arrete la requete ($request) du formulaire
            $form->handleRequest($request);
            //Verifier si le formulaire a ete soumis et verifier la validite de tous les champs
            if ($form->isSubmitted() && $form->isValid()) {
                //Recuperer les donnees du formulaire

                $categorie->setUser($this->getUser());
                $entitymanager->persist($categorie);

                $entitymanager->flush();

                //La methode propre au controller pour faire la redirection est
                return $this->redirectToRoute('app_categorie');

            }else{
                $errors=$form->getErrors(true);
                $form->clearErrors();
                //dd($errors);
            }
        }
        return $this->render('categorie/ajout.html.twig', [
            'errors'=>$errors,
            'form' => $form->createView(),
            'page'=>$page,
            'entite'=>'categorie',
        ]);
    }

    #[Route('/categorie/supprimer/{id}', name: 'app_categorie_supprimer')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Categorie $categorie, EntityManagerInterface $entitymanager, CategorieRepository $categorieRepository): Response
    {
        $entitymanager->remove($categorie);
        $entitymanager->flush();
        return $this->redirectToRoute('app_categorie');
    }

    #[Route('/categorie/editer/{id}', name: 'app_categorie_editer')]
    public function editer(Categorie $categorie, Request $request, EntityManagerInterface $entitymanager): Response
    {
        $form=$this->createForm(CategorieType::class,
            $categorie);
        $errors=[];
        if ($request->isMethod('POST')) {
            //On arrete la requete ($request) du formulaire
            $form->handleRequest($request);
            //Verifier si le formulaire a ete soumis et verifier la validite de tous les champs
            if ($form->isSubmitted() && $form->isValid()) {
                //Recuperer les donnees du formulaire
                $categorie->setUser($this->getUser());
                $entitymanager->persist($categorie);

                $entitymanager->flush();

                //La methode propre au controller pour faire la redirection est
                return $this->redirectToRoute('app_categorie');

            }else{
                $errors=$form->getErrors(true);
                $form->clearErrors();
                //dd($errors);
            }
        }
        return $this->render('categorie/ajout.html.twig', [
            'errors'=>$errors,
            'form' => $form->createView(),
        ]);
    }
   /* #[Route('/test', name: 'test')]
    public function test(ProduitRepository $produitRepository,CategorieRepository $categorieRepository): Response
    {
        $id=$categorieRepository->find(1);
        $produit=$produitRepository->rechercheParCategorieLibelle() ;
        dd($produit);
    }*/

}
