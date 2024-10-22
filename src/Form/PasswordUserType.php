<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('actualPassword', PasswordType::class, [
            'label' => "Votre mot de passe actuel",
            'attr' => [
                'placeholder' => "Indiquez votre mot de passe actuel"
            ],
            'mapped' => false
        ])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'constraints' => [
                new Length([
                    'min' => 4,
                    'max' => 30
                ])
            ],
            'first_options'  => [
                'label' => 'Votre nouveau mot de passe',
                'attr' => [
                    'placeholder' => "Choisisser votre nouveau mot de passe"
                ],
                'hash_property_path' => 'password'
            ],
            'second_options' => [
                'label' => 'Confirmez votre nouveau mot de passe',
                'attr' => [
                    'placeholder' => "Confirmez votre nouveau mot de passe"
                ]
            ],
            'mapped' => false,
        ])
        ->add('submit', SubmitType::class, [
            'label' => "Mettre à jour mon mot de passe",
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ])
        ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                    $form = $event->getForm();
                    $user = $form->getConfig()->getOptions()['data'];
                    $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];
                    
                    // 1. Récupérer le mot de passe saisi par l'utilisateur et le comparer au MDP en base de dpnnée (dans l'entité)
                    $isValid = $passwordHasher->isPasswordValid(
                        $user,
                        $form->get('actualPassword')->getData()
                    );
                    
                    // 3. si c'est diferent alors envoyer une erreur
                    if(!$isValid) {
                        $form->get('actualPassword')->addError(new FormError("Votre mot de passe actuel n'est pas conforme. Veuillez vérifier votre saisie."));
                    }
                    
        })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null
        ]);
    }
}