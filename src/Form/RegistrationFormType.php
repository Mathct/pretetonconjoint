<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as TypeIntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('email')
            ->add('img', FileType::class, [
                "label" => 'photo',
                "mapped" => false, 
            ])
            ->add('age', TypeIntegerType::class, [
                'label' => 'Âge',
                'constraints' => [
                new Assert\GreaterThanOrEqual([
                'value' => 18,
                'message' => 'Tu dois avoir au moins 18 ans pour t’inscrire.'
            ]),
            ],
                'attr' => [
                'min' => 18, 
                'max' => 120,  
            ],
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sexe',
                'choices'  => [
                'Homme' => 'homme',
                'Femme' => 'femme',
                'Intersexe' => 'intersexe', 
                'Helicoptere de combat A630' => 'helicoptere de combat A630',
                'Ne sais pas' => 'ne sais pas',
                'Autre' => 'autre'
                ],
                'placeholder' => 'Sélectionnez votre sexe'
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins 6 caractere',
    
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez accepter les thermes et conditions.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
