<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('username', TextType::class, [
            'constraints' => [
                new NotBlank(message: 'Veuillez saisir un nom d\'utilisateur.'),
                new Length(
                    min: 3,
                    minMessage: 'Votre identifiant doit contenir au moins 3 caractères.',
                    max: 180,
                    maxMessage: 'Votre identifiant doit contenir au maximum 180 caractères.',
                ),
            ],
        ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir une adresse e-mail.',
                    ),
                    new Email(
                        message: 'L\'adresse e-mail renseignée n\'est pas valide.',
                    ),
                    new Length(
                        max: 100,
                    ),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir un mot de passe.',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit contenir au moins 6 caractères.',
                        max: 4096,
                        maxMessage: 'Votre mot de passe doit contenir au maximum 4096 caractères.',
                    ),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les conditions générales',
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter les conditions générales.',
                    ),
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
