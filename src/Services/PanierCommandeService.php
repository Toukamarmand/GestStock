<?php

namespace App\Services;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\LigneVente;
use App\Entity\Vente;
use App\Panier\ProduitCard;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class PanierCommandeService
{
    public const SESSION_PANIER = "panierCommande";
    private RequestStack $requestStack;
    private ProduitRepository $produitRepository;
    private EntityManagerInterface $entityManager;



    public function __construct(ProduitRepository $produitRepository, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->produitRepository = $produitRepository;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function increment($produit)
    {
        $produit = $this->produitRepository->find($produit);
        $session = $this->requestStack->getSession();
        $pannier = $session->get(self::SESSION_PANIER, []);
        if (empty(($pannier))) {
            $lignePanier = new ProduitCard();
            $lignePanier->setProduit($produit);
            $lignePanier->setPrixUnitaire($produit->getPrixAchat());
            $pannier[] = $lignePanier;
        } else {
            //1-ON ITERE SUR CHAQUE LIGNE DU PANIER
            foreach ($pannier as $key => $lignePanier) {
                //2-On compare l'identifiant du produit de la ligne en cours avec
                // l'identifiant du produit que l'on veut ajouter
                if ($lignePanier->getProduit()->getId() == $produit->getId()) {
                    //On met a jour la ligne au cas ou les identifiants correspondent
                    $lignePanier->setQte($lignePanier->getQte() + 1);
                    $pannier[$key] = $lignePanier;
                    break;
                }
            }

            //On ajoute une nouvelle ligne si on a pas trouve de correspondance
            if ($lignePanier->getProduit()->getId() !== $produit->getId()) {
                $lignePanier = new ProduitCard();
                $lignePanier->setProduit($produit);
                $lignePanier->setPrixUnitaire($produit->getPrixAchat());
                $pannier[] = $lignePanier;
            }
        }
        $session->set(self::SESSION_PANIER, $pannier);
        return true;
    }

    public function decrementer($produit)
    {
        $produit = $this->produitRepository->find($produit);
        $session = $this->requestStack->getSession();
        $pannier = $session->get(self::SESSION_PANIER, []);
        if (empty(($pannier))) {
            $session->getFlashBag()->add('danger', 'Le panier est vide');
        } else {
            //1-ON ITERE SUR CHAQUE LIGNE DU PANIER
            foreach ($pannier as $key => $lignePanier) {
                //2-On compare l'identifiant du produit de la ligne en cours avec
                // l'identifiant du produit que l'on veut ajouter
                if ($lignePanier->getProduit()->getId() == $produit->getId()) {
                    //On met a jour la ligne au cas ou les identifiants correspondent
                    $qte = $lignePanier->getQte();
                    if ($qte <= 1) {
                        $session->getFlashBag()->add('danger', 'La quantite doit etre superieur a 1');
                    } else {
                        $lignePanier->setQte($lignePanier->getQte() - 1);
                    }
                    $pannier[$key] = $lignePanier;
                    break;
                }
            }
        }
        $session->set(self::SESSION_PANIER, $pannier);
        return true;
    }

    public function supprimerlignepanier($produit)
    {
        $produit = $this->produitRepository->find($produit);
        $session = $this->requestStack->getSession();
        $pannier = $session->get(self::SESSION_PANIER, []);
        //1-Parcourir le tableau pour trouver et supprimer l'element
        foreach ($pannier as $key => $lignePanier) {
            //2-On compare l'identifiant du produit de la ligne en cours avec
            // l'identifiant du produit que l'on veut ajouter
            if ($lignePanier->getProduit()->getId() === $produit->getId()) {
                //On met a jour la ligne au cas ou les identifiants correspondent
                unset($pannier[$key]);
                //Supprimer l'element
                break;
            }
        }
        //Reindexer le tableau pour eviter les trous dans les cles
        $pannier = array_values($pannier);

        $session->set(self::SESSION_PANIER, $pannier);
        $session->getFlashBag()->add('success', "Le produit " . $produit->getDesignation() . " a été supprimé");

    }

    public function viderpanier()
    {
        $session = $this->requestStack->getSession();
        $pannier = [];
        $session->set(self::SESSION_PANIER, $pannier);
        $session->getFlashBag()->add('success', 'Le panier est vide');
    }

    public function ValiderPanier()
    {
        $session = $this->requestStack->getSession();
        $pannier = $session->get(self::SESSION_PANIER, []);
        if (empty(($pannier))) {
            $session->getFlashBag()->add('danger', 'Le panier est vide');
        } else {
            $commande = new Commande();
            $code = random_int('5', '1000');
            $date = new \DateTime();
            $commande->setCode($code);
            $commande->setDate($date);
            $commande->setStatut(false);
            //$commande->setUser();
            $this->entityManager->persist($commande);

            foreach ($pannier as $lignePanier) {
                $ligne = new LigneCommande();
                $produit = $this->produitRepository->find($lignePanier->getProduit()->getId());
                $ligne->setPrixUnitaire($lignePanier->getprixUnitaire());
                $ligne->setQte($lignePanier->getqte());
                $ligne->setProduit($produit);
                $ligne->setCommande($commande);
                //$ligne->getDateExpiration($lignePanier->getdateExpiration());
                $ligne->setDateExpiration($lignePanier->getDateExpiration());
                $this->entityManager->persist($ligne);
            }
            $this->entityManager->flush();
            $session->set(self::SESSION_PANIER, []);
        }

    }

    public function modifier($id, $qte)
    {
        $session = $this->requestStack->getSession();
        $pannier = $session->get(self::SESSION_PANIER, []);
        foreach ($pannier as $key => $lignePanier) {
            //2-On compare l'identifiant du produit de la ligne en cours avec
            // l'identifiant du produit que l'on veut ajouter
            if ($lignePanier->getProduit()->getId() == $id) {
                //On met a jour la ligne au cas ou les identifiants correspondent
                $lignePanier->setQte($qte);
                $pannier[$key] = $lignePanier;
                break;
            }
        }
        $session->set(self::SESSION_PANIER, $pannier);
        return true;
    }
    public function modifierdate($id, $date)
    {
        $session = $this->requestStack->getSession();
        $pannier = $session->get(self::SESSION_PANIER, []);
        $dat=\DateTime::createFromFormat('Y-m-d',$date);
        foreach ($pannier as $key => $lignePanier) {
            //2-On compare l'identifiant du produit de la ligne en cours avec
            // l'identifiant du produit que l'on veut ajouter
            if ($lignePanier->getProduit()->getId() == $id) {
                //On met a jour la ligne au cas ou les identifiants correspondent
                $lignePanier->setDateExpiration($dat);
                break;
            }
        }
        $session->set(self::SESSION_PANIER, $pannier);
        return true;
    }
}