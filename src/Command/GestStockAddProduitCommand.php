<?php

namespace App\Command;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'gestStock:addProduit',
    description: "Ajouter un produit a l'application",
)]
class GestStockAddProduitCommand extends Command
{
    private CategorieRepository $categorieRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(CategorieRepository $categorieRepository,
                                EntityManagerInterface $entityManager,ValidatorInterface $validator)
    {
        parent::__construct();
        $this->categorieRepository = $categorieRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        $io->text('Enregistrer un produit');
        $productName=$io->askQuestion(new Question('Entrez la désignation du produit'));
        $productprixAchat=$io->askQuestion(new Question('Entrez le prix d\'achat du produit'));
        $productprixVente=$io->askQuestion(new Question('Entrez le prix de vente du produit'));
        $productqteAlerte=$io->askQuestion(new Question('Entrez la quantité alerte',0));

        $libelles=array_map(function (Categorie $categorie){
            return $categorie->getlibelle();
        },$this->categorieRepository->findAll());
        $libelle=$io->choice('Entrez la catégorie du produit',$libelles);
        $categorie=$this->categorieRepository->findOneBy(['libelle'=>$libelle]);

        $produit=new  Produit();
        $produit->setDesignation($productName);
        $produit->setPrixAchat($productprixAchat);
        $produit->setPrixVente($productprixVente);
        $produit->setQteAlerte($productqteAlerte);
        $produit->setCategorie($categorie);
        $errors=$this->validator->validate($produit);
        if (count($errors)>0){
            $io->error('Impossible d\' enregistré le produit');

            foreach ($errors as $error){
                $io->error($error->getMessage());
            }
            return Command::INVALID;
        }else{
            $this->entityManager->persist($produit);
            $this->entityManager->flush();

            $io->success('Produit enregistré');
        }

        return Command::SUCCESS;
    }
}
