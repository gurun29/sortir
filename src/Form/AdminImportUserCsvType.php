<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminImportUserCsvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('nom')
//            ->add('prenom')
//            ->add('telephone')
//            //->add('roles')
//            ->add('mail')
//            //->add('motPasse')
//            ->add('administrateur')
//            ->add('actif')
//            ->add('pseudo')
            //->add('estRattacheA')
            //->add('sortiesInscrit')
            //->add('imagesParticipant')
            //->add('csv', FileType::class)
            ->add('csv', FileType::class,[
                'label' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
