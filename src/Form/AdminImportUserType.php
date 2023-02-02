<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminImportUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        $countries = array(
//            'au' => 'Australia',
//            'at' => 'Austria',
//            'az' => 'Azerbaijan',
//
//        );

        $builder
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
//            ->add('roles', ChoiceType::class, [
//                'choices' => [
//                    'Cancelled' => 'canceled',
//                    'ended' => 'ended',
//                    'returning' => 'returning'
//                ],
//                'multiple'=>false
//            ])
            ->add('mail')
//            ->add('motPasse')
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('administrateur')
            //->add('actif')
            ->add('actif',null,[
                'value' => true
            ])
            ->add('pseudo')
            ->add('estRattacheA',EntityType::class,
                            [
                                'label' => 'Campus',
                                // quelle est la classe à afficher ici ?
                                'class' => Campus::class,
                                // // quelle propriété utiliser pour les <option> dans la liste déroulante ?
                                'choice_label' => 'nom',
                                'placeholder' => '--Choose a campus--'
                            ])
            //->add('sortiesInscrit')
            ->add('images', FileType::class,[
                'label' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            //->add('csv', FileType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
