<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'=> 'Votre prénom',
                //longueur(min,max) dans un tableau associatif
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                'attr'=>  [
                    'placeholder' => 'Merci de saisir votre prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label'=> 'Votre nom',
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                'attr'=>  [
                    'placeholder' => 'Merci de saisir votre nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 60
                ]),
                'attr'=>  [
                    'placeholder' => 'Merci de saisir votre adresse email'
                ]
            ])
            //au lieu d'utiliser un PasswordType avec confirmation dans un second champ, on utilise la classe RepeatedType qui permet de générer 2 champs différents pour une même propriété et dont le contenu doit être identique
            ->add('password', RepeatedType::class, [
                //propriété relative au RepeatedType (le passwordType est ici le sous type du RepeatedType) qui doivent être indiquées
                'type'=> PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques',
                'required' => true, //champ obligatoire
                'first_options'=> [
                    'label'=> 'Votre mot de passe',
                    'attr'=> [
                        'placeholder' =>'Merci de saisir votre mot de passe'
                        ]
                ],
                'second_options'=> [
                    'label'=> 'Confirmez votre mot de passe',
                    'attr'=> [
                        'placeholder'=>'Merci de confirmer votre mot de passe'
                        ]
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
