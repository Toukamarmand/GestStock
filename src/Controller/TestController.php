<?php

namespace App\Controller;


use App\Repository\JournalisationRepository;
use App\Services\ProduitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test/jour', name: 'app_test_jour')]
    public function index(JournalisationRepository $journalisationRepository): Response
    {
        dd($journalisationRepository->findAll());
        return $this->render('accueil/index.html.twig', [

        ]);
    }
}
