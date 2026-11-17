<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation',TextType::class,
                //Pour styliser l'input du libelle
                [
                    //'required'=>false,
                    'attr' => [
                        'class' => '',
                        'placeholder' => 'Entrer la designation du produit'
                    ],
                ])
            ->add('prixAchat',NumberType::class)
            ->add('prixVente',NumberType::class)
            //->add('photo')
            ->add('qteAlerte', IntegerType::class)
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                //Le choix du label ou select qui affichera la categorie
                'choice_label' => 'libelle',
            ])
            ->add('photoFile', VichFileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
