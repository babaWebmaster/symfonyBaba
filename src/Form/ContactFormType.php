<?php
// src/Form/ContactFormType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
                'constraints' => [ new Assert\NotBlank(['message'=>'Veuillez saisir votre nom.']),
            ],
            ])
             ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Votre prénom'],
                'constraints' => [ new Assert\NotBlank(['message'=>'Veuillez saisir votre prénom.']),
            ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'attr' => ['placeholder' => 'Votre email'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir votre adresse e-mail.'
                    ]),
                    new Assert\Email([
                        'message' => 'L\'adresse e-mail n\'est pas valide.'
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'N° téléphone',
                'attr' => ['placeholder' => 'Votre téléphone'],
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^(\+33|0)[1-9]([-. ]?[0-9]{2}){4}$/', 
                        'message' => 'Le numéro de téléphone n\'est pas valide.'
                    ]),
                ],
            ])           
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => ['rows' => 6],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Vous devez indiquer un message'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas de données spécifiques pour ce formulaire
        ]);
    }
}
?>