<?php

namespace App\Panier;

use App\Entity\Produit;

class ProduitCard
{
    private  Produit $produit;
    private int $qte=1;
    private  float $prixUnitaire;
    private ?\DateTimeInterface $dateExpiration = null;

    /**
     * @return Produit
     */
    public function getProduit(): Produit
    {
        return $this->produit;
    }

    /**
     * @param Produit $produit
     * @return ProduitCard
     */
    public function setProduit(Produit $produit): ProduitCard
    {
        $this->produit = $produit;
        return $this;
    }

    /**
     * @return int
     */
    public function getQte(): int
    {
        return $this->qte;
    }

    /**
     * @param int $qte
     * @return ProduitCard
     */
    public function setQte(int $qte): ProduitCard
    {
        $this->qte = $qte;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrixUnitaire(): float
    {
        return $this->prixUnitaire;
    }

    /**
     * @param float $prixUnitaire
     * @return ProduitCard
     */
    public function setPrixUnitaire(float $prixUnitaire): ProduitCard
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    /**
     * @param \DateTimeInterface|null $dateExpiration
     * @return ProduitCard
     */
    public function setDateExpiration(?\DateTimeInterface $dateExpiration): ProduitCard
    {
        $this->dateExpiration = $dateExpiration;
        return $this;
    }

    /**
     * @return float
     */
    public function getSousTotal(): float
    {
        return $this->getPrixUnitaire()*$this->getQte();
    }

    public function montant(): float
    {
        return $this->getSousTotal()+$this->getSousTotal();
    }
}