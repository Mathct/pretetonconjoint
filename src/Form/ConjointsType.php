<?php

namespace App\Form;

use App\Entity\Conjoints;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConjointsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', null, [
                'label' => 'Pseudo',
            ])
            ->add('age', null, [
                'label' => 'Age',
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                'Femme'=> 'Femme',
                'Homme' => 'Homme',
                ],
                'placeholder' => 'Choisir',
            ])
            ->add('description', null, [
                'label' => 'Description',
            ])
            ->add('img', FileType::class, [
                "label" => 'Photo',
                "mapped" => false, 
            ])
            ->add('style', ChoiceType::class, [
                'label'=>'Style',
                'choices' => [
                'Aventurier(e)'=> 'Aventurier(e)',
                'Prince(sse)' => 'Prince(sse)',
                'Geek(ette)' => 'Geek(ette)',
                'Hetero curieux(se)' => 'Hete)ro curieux(se)',
                'Sportif(ve) extrême' => 'Sportif(ve) extrême',
                'Sauvage' => 'Sauvage',
                ],
                 'placeholder' => 'Choisir',
            ])

            ->add('categorie', ChoiceType::class, [
                'label'=>'Categorie',
                'choices' => [
                'Avion de chasse' => 'Avion de chasse',
                'Bon plan' => 'Bon plan',
                'Présentable' => 'Présentable',
                'Sur un malentendu' => 'Sur un malentendu',
                'Oui mais dans le noir' => 'Oui mais dans le noir',
                'Fin de soirée' => 'Fin de soirée',
            ],
            'placeholder' => 'Choisir',
            ])

            ->add('proprietaire', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])

            // ->add('emprunteur', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conjoints::class,
        ]);
    }
}
