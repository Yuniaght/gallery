<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Technic;
use App\Entity\Work;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class WorkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', textType::class, [
                'label' => 'Titre',
            ])
            ->add('imageFile', vichImageType::class, [
                'label' => 'Image',
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => true,
            ])
            ->add('description', textareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'rows' => 10,
                ]
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de création de l\'oeuvre',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('technic', EntityType::class, [
                'class' => Technic::class,
                'choice_label' => 'name',
            ])
            ->add('artist', EntityType::class, [
                'class' => Artist::class,
                'choice_label' => function (Artist $artist) {
                return $artist->getFirstname() . ' ' . $artist->getLastname() ." - " .$artist->getPseudonym();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Work::class,
        ]);
    }
}
