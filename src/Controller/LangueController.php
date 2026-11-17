<?php

namespace App\Controller;


use App\Services\ProduitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\LocaleSwitcher;

class LangueController extends AbstractController
{
    #[Route('/changer/langue', name: 'app_changer_langue')]
    public function index(LocaleSwitcher $localeSwitcher, Request $request): Response
    {
        $langue=($request->get('langue'))?? $localeSwitcher->getLocale();
        $request->getSession()->set('langue',$langue);
        return $this->redirectToRoute('app_produit_ajout', [
        ]);
    }
}
