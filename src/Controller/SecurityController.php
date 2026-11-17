<?php

namespace App\Controller;


use App\Repository\JournalisationRepository;
use App\Services\ProduitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index( AuthenticationUtils $authenticationUtils): Response
    {
        $erreur=$authenticationUtils->getLastAuthenticationError();
        $lasrUser=$authenticationUtils->getLastUsername();
        //$this->addFlash('danger',$erreur);
       return $this->render('securite/login.html.twig',[
           'entite'=>'accueil',
           'page'=>'app_accueil',
           'erreur'=>$erreur,
           'lastUser'=>$lasrUser,
       ]);
    }
    #[Route('/logout', name: 'app_log_out')]
    public function logout():void
    {

    }
}
