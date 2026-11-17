<?php

namespace App\Dto;

use App\Entity\Categorie;
use App\Entity\Produit;

class ProduitDto
{
    public ?int $id = null;
    
    public ?string $designation = null;
    
    public ?float $prixAchat = 0;
    
    public ?float $prixVente = 0;

   
    public ?string $photo = null;

   
    public ?int $qteAlerte = 0;


    public ?int $categorieId = null;
    public ?string $categorielibelle = null;
    public function __construct(Produit $produit)
    {
        $this->id=$produit->getId();
        $this->designation=$produit->getDesignation();
        $this->prixAchat=$produit->getPrixAchat();
        $this->prixVente=$produit->getPrixVente();
        $this->qteAlerte=$produit->getQteAlerte();
        $this->photo=$produit->getPhoto();
        $this->categorieId=$produit->getCategorie()->getId();
        $this->categorielibelle=$produit->getCategorie()->getlibelle();
    }

}