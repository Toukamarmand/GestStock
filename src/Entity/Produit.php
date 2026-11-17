<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[Vich\Uploadable]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(min: 3,max: 50,minMessage: "Maximum 3 caractères",maxMessage: "Maximum 50 caractères")]
    private ?string $designation = null;

    #[ORM\Column]
    #[Assert\Positive(message: "Le prix doit etre superieur a 0")]
    private ?float $prixAchat = 0;

    #[ORM\Column]
    #[Assert\Positive(message: "Le prix doit etre superieur a 0")]
    #[Assert\GreaterThan(propertyPath: "prixAchat", message: "Le prix de vente doit etre superieur au prix d'achat")]
    private ?float $prixVente = 0;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $photo = null;

    #[Vich\UploadableField(mapping: 'produits',fileNameProperty: 'photo')]
    private ?File $photoFile=null;
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt=null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: "Le prix doit etre egale ou superieur a 0")]
    private ?int $qteAlerte = 0;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, LigneCommande>
     */
    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $ligneCommandes;

    /**
     * @var Collection<int, LigneVente>
     */
    #[ORM\OneToMany(targetEntity: LigneVente::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $ligneVentes;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user = null;

    #[ORM\Column(length: 100)]
    private ?string $reference = null;


    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
        $this->ligneVentes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getPrixAchat(): ?float
    {
        return $this->prixAchat;
    }

    public function setPrixAchat(float $prixAchat): static
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    public function getPrixVente(): ?float
    {
        return $this->prixVente;
    }

    public function setPrixVente(float $prixVente): static
    {
        $this->prixVente = $prixVente;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    /**
     * @param File|null $photoFile
     * @return Produit
     */
    public function setPhotoFile(?File $photoFile): Produit
    {
        $this->photoFile = $photoFile;
        if ($photoFile!==null){
            $this->updatedAt= new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeImmutable|null $updatedAt
     * @return Produit
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): Produit
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


    public function getQteAlerte(): ?int
    {
        return $this->qteAlerte;
    }

    public function setQteAlerte(int $qteAlerte): static
    {
        $this->qteAlerte = $qteAlerte;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setProduit($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getProduit() === $this) {
                $ligneCommande->setProduit(null);
            }
        }

        return $this;
    }



/**
 * @return Collection<int, LigneVente>
 */
public function getLigneVentes(): Collection
{
    return $this->ligneVentes;
}

public function addLigneVente(LigneVente $ligneVente): static
{
    if (!$this->ligneVentes->contains($ligneVente)) {
        $this->ligneVentes->add($ligneVente);
        $ligneVente->setProduit($this);
    }

    return $this;
}

public function removeLigneVente(LigneVente $ligneVente): static
{
    if ($this->ligneVentes->removeElement($ligneVente)) {
        // set the owning side to null (unless already changed)
        if ($ligneVente->getProduit() === $this) {
            $ligneVente->setProduit(null);
        }
    }

    return $this;
}

    public function getQtecommande(): ?int
    {
        $qte=0;
        $qtes=0;
        foreach ($this->getLigneCommandes()->toArray() as $ligne){
            /*$vente=$this->getLigneVentes()->toArray() ;*/
            if($ligne->getCommande()->isStatut()){
                $qte+=$ligne->getQte();
            }

        }
        foreach ($this->getLigneVentes()->toArray() as $lignes){
            $qtes+=$lignes->getQte();
        }
        $stock=$qte-$qtes;
        return $stock;
    }

    public function getQtevendue(): ?int
    {
        $qtes=0;
        foreach ($this->getLigneVentes()->toArray() as $lignes){
            $qtes+=$lignes->getQte();
        }
        return $qtes;
    }
    public function getQtecommander(): ?int
    {
        $qtescommande=0;
        foreach ($this->getLigneCommandes()->toArray() as $lignes){
            $qtescommande+=$lignes->getQte();
        }
        return $qtescommande;
    }
    //prix achat moyen
    public  function getprixAchatMoyen() : ?float
    {
        $qtemoyenne=0;
        $prixachatmoyen=0;
        $prix=0;
        foreach ($this->getLigneCommandes()->toArray() as $lignes) {
            $prixachatmoyen+=$lignes->getPrixUnitaire()*$lignes->getQte();
            $qtemoyenne+=$lignes->getQte();
        }

        $prix=$prixachatmoyen/$qtemoyenne;
        return $prix;
    }

    //Montant pour une vente
    public  function getMontantvente():?float
    {
       $montant=$this->getQtevendue()*$this->getPrixVente();
       return $montant;
    }
    //Montant
    public  function getMontantcommande():?float
    {
        $montant=$this->getQtecommander()*$this->getprixAchatMoyen();
        return $montant;
    }
  /*   public  function getprofit(ProduitRepository $produitRepository) : ?float
  {
      $produit=$produitRepository->findAll();
      $nbreProduit=count($produit);
      $montantprofit=0;
      for ($i=0;$i<$nbreProduit;$i++){
          $montant=($this->getPrixVente()-$this->getprixAchatMoyen())*$this->getQtevendue();
          $montantprofit=+$montant;
      }
      return $montantprofit;
  }*/
    public  function getStockcolor() : ?string
    {
        $stock=$this->getQtecommande();
        $qteAlerte=$this->getQteAlerte();
        if($stock==0){
            $color='text-danger';
        }
        elseif ($stock<=$qteAlerte){
            $color='text-warning';
        }
        else{
            $color='text-success';
        }
        return $color;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }



}
