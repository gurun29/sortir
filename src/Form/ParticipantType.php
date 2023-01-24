<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
            //->add('motPasse')
            ->add('administrateur')
            ->add('actif')
            ->add('pseudo')
            ->add('sortiesInscrit')
            ->add('getEstRattacheA', EntityType::class, [
                'class'=>Campus::class,
                'choice_label'=>'nom',
                'placeholder'=>'saisir un campus',
                'query_builder'=>function(CampusRepository $campusRepository)
                {
                    return $campusRepository->createQueryBuilder('nom');
                    //return $campusRepository->createQueryBuilder('name')->orderBy('nom','ASC');
                }
            ])
            //->add('estRattacheA')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
