<?php

namespace App\Controller;

use App\Entity\Vente;
use App\Repository\ProduitRepository;
use App\Services\SendMailerService;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RapportController extends AbstractController
{
    #[Route('/rapport/commande', name: 'app_rapport_commande',methods: ['POST','GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, ProduitRepository $produitRepository,Pdf $pdf,SendMailerService $sendMailerService): PdfResponse|Response
    {
        $produit=$produitRepository->findAll();
       if ($request->isMethod('POST') && $request->get('imprimer')!==null){
           $route= $this->render('pdf/rapportCommande.html.twig', [
               'listeproduit'=>$produit,
               ]);
           $get=$pdf->getOutputFromHtml($route);
           $email='jkmarc8173@gmail.com';
           $sendMailerService->sendfile($email,$get);
           return new PdfResponse(
               $get,
           $pdf->getOutputFromHtml($route),
           'commande.pdf'
       );
       }
        return $this->render('rapport/rapportCommande.html.twig', [
            'listeproduit'=>$produit,
            'entite' => 'rapport',
            'page'=>'app_rapport_commande',
        ]);
    }
    #[Route('/rapport/vente', name: 'app_rapport_vente')]
    #[IsGranted('ROLE_ADMIN')]
    public function vente(ProduitRepository $produitRepository,Request $request,Pdf $pdf,SendMailerService $sendMailerService): Response
    {
        //Pour permettre la previsualisation
        $pdf->setOption('enable-local-file-access',true);
        //Pour modifier la hauteur
        $pdf->setOption('page-height',205);
        //Largeur de la page
        $pdf->setOption('page-width',185);
        $pdf->setOption('margin-left',5);
        $pdf->setOption('margin-right',5);
        $pdf->setOption('margin-top',2);
        $produit=$produitRepository->findAll();
             if($request->isMethod('POST') && $request->get('print')!==null){
                  $route=$this->renderView('pdf/rapportVente.html.twig',[
                      'listeproduit'=>$produit,
                  ]);
                 $get=$pdf->getOutputFromHtml($route);
                 $email='jkmarc8173@gmail.com';
                 $sendMailerService->sendfile($email,$get);
                      return new PdfResponse(
                          $get,
                          'Liste_vente.pdf',
                          'application/pdf',
                          'inline'
                      );
              }
        return $this->render('rapport/index.html.twig', [
            'listeproduit'=>$produit,
            'entite' => 'rapport',
            'page'=>'app_rapport_vente',
        ]);
    }



}
