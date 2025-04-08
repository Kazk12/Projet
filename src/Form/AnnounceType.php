<?php

namespace App\Form;

use App\Entity\Announce;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnnounceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Détermine si c'est un formulaire de création ou de modification
        $isEdit = $builder->getData() && $builder->getData()->getId();
        
        $fileConstraints = [
            new File([
                'maxSize' => '16M',
                'maxSizeMessage' => 'Le fichier est trop volumineux. La taille maximale autorisée est de 2 Mo.',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                ],
                'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG ou GIF)',
            ])
        ];
        
        // Ajout de la contrainte NotBlank seulement pour la création
        if (!$isEdit) {
            $fileConstraints[] = new NotBlank([
                'message' => 'Veuillez télécharger une image',
            ]);
        }

        $builder
            ->add('thumbnailFile', FileType::class, [
                'label' => 'Image',
                'required' => !$isEdit, // Obligatoire en création, facultatif en édition
                'mapped' => true,
                'constraints' => $fileConstraints,
                'help' => $isEdit ? 'Laissez vide pour conserver l\'image actuelle' : 'Formats acceptés : JPG, PNG, GIF (max 16Mo)',
            ])
            ->add('rate', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Note',
                'attr' => ['class' => 'star-rating'],
            ])
            ->add('content')
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'label' => 'Livre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announce::class,
        ]);
    }
}