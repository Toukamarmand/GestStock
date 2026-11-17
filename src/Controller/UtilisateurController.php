<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Form\CategorieType;
use App\Form\UtilisateurType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use PharIo\Manifest\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function PHPUnit\Framework\any;

class UtilisateurController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $listusers = $utilisateurRepository->findAll();

        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'CategorieController',
            'users' => $listusers,
            'page'=>'app_user',
            'entite'=>'user',
        ]);
    }
    #[Route('/oneUser', name: 'app_oneUser')]
    public function oneUser(UtilisateurRepository $utilisateurRepository): Response
    {
        $listusers = $this->getUser();
        $username=$this->getUser()->getuserName();
        $role=$this->getUser()->getrole();
        //dd($listusers);
        return $this->render('utilisateur/profil.html.twig', [
            'controller_name' => 'CategorieController',
            'username' => $username,
            'role' => $role,
            'page'=>'app_user',
            'entite'=>'user',
        ]);
    }
    #[Route('/editProfil', name: 'app_editProfil')]
    public function edit(UtilisateurRepository $utilisateurRepository, Request $request, EntityManagerInterface $entitymanager): Response
    {
        /*$utilisateur=$this->getUser();
        $nom=$utilisateur->getUserName();
        $password=$utilisateur->getPassword();*/
        $username=$this->getUser()->getuserName();
        $role=$this->getUser()->getrole();
        $id=$this->getUser()->getid();
        $login=$request->get('login');
        $password=$request->get('nom');
        //dd($login, $password);
        if (isset($login) && isset($password)){
            $user=$utilisateurRepository->find($id);
            //$user=new Utilisateur();
            $user->setUserName($login);
            $user->setPassword($password);
            $entitymanager->persist($user);
            $entitymanager->flush();
            //dd($login, $password);
            $this->redirectToRoute('app_oneUser');
        }
        //dd($listusers);
        return $this->render('utilisateur/profil.html.twig', [
            'controller_name' => 'CategorieController',
            'page'=>'app_user',
            'entite'=>'user',
            'username' => $username,
            'role' => $role,
        ]);
    }

    #[Route('/changePassword', name: 'app_changePassword')]
    public function changePassword(UtilisateurRepository $utilisateurRepository, Request $request, EntityManagerInterface $entitymanager): Response
    {

        /*$utilisateur=$this->getUser();
        $nom=$utilisateur->getUserName();
        $password=$utilisateur->getPassword();*/
        $username=$this->getUser()->getuserName();
        $role=$this->getUser()->getrole();
        $id=$this->getUser()->getid();
        $pass=$this->getUser()->getpassword();
        $password=$request->get('password');
        $newpassword=$request->get('newpassword');
        $renewpassword=$request->get('renewpassword');
        //dd($newpassword,$renewpassword, $password,$pass);
        if (isset($password)){
            //$user=new Utilisateur();
            if ($password===$pass){
                if ($newpassword!==$renewpassword){
                    $this->addFlash('danger','Les mots de passe ne correspondent pas');
                }else{
                    $user=$utilisateurRepository->find($id);
                    //dd($user);
                    $user->setPassword($password);
                    $entitymanager->persist($user);
                    $entitymanager->flush();
                    $this->addFlash('success','Mot de passe modifié');
                    $this->redirectToRoute('app_oneUser');
                }
            }else{
                $this->addFlash('danger','Mot de passe incorrect');
            }
            //dd($login, $password);
        }
        //dd($listusers);
        return $this->render('utilisateur/profil.html.twig', [
            'controller_name' => 'CategorieController',
            'page'=>'app_user',
            'entite'=>'user',
            'username' => $username,
            'role' => $role,
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

    #[Route('/user/ajout', name: 'app_user_ajout')]
    public function ajout(Request $request, EntityManagerInterface $entitymanager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $page='app_user_ajout';
        $user=new Utilisateur();
        $form=$this->createForm(UtilisateurType::class,
        $user);
        $errors=[];
        if ($request->isMethod('POST')) {
            //On arrete la requete ($request) du formulaire
            $form->handleRequest($request);
            //Verifier si le formulaire a ete soumis et verifier la validite de tous les champs
            if ($form->isSubmitted() && $form->isValid()) {
                $hashpassword=$passwordHasher->hashPassword($user,$user->getPassword());
                $user->setPassword($hashpassword);
                //Recuperer les donnees du formulaire

                //$user->setUser($this->getUser());
                $entitymanager->persist($user);

                $entitymanager->flush();

                //La methode propre au controller pour faire la redirection est
                return $this->redirectToRoute('app_user');

            }else{
                $errors=$form->getErrors(true);
                $form->clearErrors();
                //dd($errors);
            }
        }
        return $this->render('utilisateur/ajout.html.twig', [
            'errors'=>$errors,
            'form' => $form->createView(),
            'page'=>$page,
            'entite'=>'user',
        ]);
    }

    #[Route('/user/supprimer/{id}', name: 'app_user_supprimer')]
    public function delete(Utilisateur $utilisateur, EntityManagerInterface $entitymanager, CategorieRepository $categorieRepository): Response
    {
        $entitymanager->remove($utilisateur);
        $entitymanager->flush();
        return $this->redirectToRoute('app_user');
    }

    #[Route('/user/editer/{id}', name: 'app_user_editer')]
    public function editer(Utilisateur $utilisateur, Request $request, EntityManagerInterface $entitymanager): Response
    {
        $form=$this->createForm(UtilisateurType::class,
            $utilisateur);
        $errors=[];
        if ($request->isMethod('POST')) {
            //On arrete la requete ($request) du formulaire
            $form->handleRequest($request);
            //Verifier si le formulaire a ete soumis et verifier la validite de tous les champs
            if ($form->isSubmitted() && $form->isValid()) {
                //Recuperer les donnees du formulaire
                //$categorie->setUser($this->getUser());
                $entitymanager->persist($utilisateur);

                $entitymanager->flush();

                //La methode propre au controller pour faire la redirection est
                return $this->redirectToRoute('app_user');

            }else{
                $errors=$form->getErrors(true);
                $form->clearErrors();
                //dd($errors);
            }
        }
        return $this->render('utilisateur/ajout.html.twig', [
            'errors'=>$errors,
            'form' => $form->createView(),
            'page'=>'app_user_supprimer',
            'entite'=>'user',
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
