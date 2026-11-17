<?php

namespace App\EventSubscriber;

use App\Entity\Journalisation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class SaveJournalisationSubscriber implements EventSubscriberInterface
{


    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;


    public function __construct(EntityManagerInterface $entityManager,TokenStorageInterface $tokenStorage)
    {

        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $routeNonAllouer=[
            '_wdt',
            '_profiler',
            'app_ajaxNbreProduitExpirer',
            'app_ajaxNbreProduitExpirer7Jours',
            'app_ajaxNbreProduitEpuiser',
            'app_ajaxpanier',
            'app_ajax_supprimerLigne',
            'app_ajax_incrementer',
            'app_ajax_decrementer',
            'app_ajax_modifierqte',
            'app_ajax_panierCommande',
            'app_ajax_decrementerCommande',
            'app_ajax_modifierqteCommande',
        ];
        //dd($event->getRequest());
        $route=$event->getRequest()->get('_route');
        $date=new \DateTime();
        //dd($route);
        $token=$this->tokenStorage->getToken();
        if ($token !== null){
            if ($route!==null) {
                $user = $this->tokenStorage->getToken()->getUser();
                if (!in_array($route, $routeNonAllouer)) {
                    $journalisation = new Journalisation();
                    $journalisation->setRoute($route);
                    $journalisation->setDate($date);
                    $journalisation->setUser($user);
                    $this->entityManager->persist($journalisation);
                    $this->entityManager->flush();
                }
            }
        }

        //dd($event->getRequest());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
