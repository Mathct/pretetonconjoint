<?php

namespace App\Form;

use App\Entity\Commentaires;
use App\Entity\Conjoints;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire', null, [
                'label' => 'Commentaires',
            ])

            ->add('note', ChoiceType::class, [
                'label'=>'Note',
                'choices' => [
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
            ],
            'placeholder' => 'Choisir',
            ])

            ->add('conjoint', EntityType::class, [
                'class' => Conjoints::class,
                'choice_label' => 'id',
            ])

            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
         

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }
}
