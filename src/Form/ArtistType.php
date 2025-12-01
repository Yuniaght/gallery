<?php

namespace App\Form;

use App\Entity\Artist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArtistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', textType::class,
            [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom',
                ]
            ])
            ->add('lastName' , textType::class,
            [
                'label' => 'Prenom',
                'attr' => [
                    'placeholder' => 'Prenom',
                ]
            ])
            ->add('pseudonym', textType::class,
            [
                'label' => 'Pseudonyme',
                'attr' => [
                    'placeholder' => 'Pseudonyme',
                ]
            ])
            ->add('dateOfBirth', BirthdayType::class,  [
                'widget' => 'single_text',
                'label' => 'Date de naissance',
            ])
            ->add('dateOfDeath', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date de décès',
                'help' => 'Laissez vide si l\'artiste est toujours en vie'
            ])
            ->add('imageFile', vichImageType::class, [
                'label' => 'Photo de l\'artiste',
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => true,
            ])
            ->add('description', textareaType::class,
            [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description',
                    'rows' => 10,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
        ]);
    }
}
