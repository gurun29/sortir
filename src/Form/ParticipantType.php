<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use Doctrine\DBAL\Types\TextType;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('nom', TextType::class,
                [
                    'label' => 'Nom',
                    'placeholder' => "votre nom svp",
                ])*/
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
            //->add('password')
            ->add('mdp',null,
                [
                    'label' => 'Mot de passe',
                    'mapped' => false,
                ]
            )
            ->add('mdp2',null,
                [
                    'label' => 'Confirmez le mot de passe',
                    'mapped' => false,
                ]
            )
            /*->add('mdp', Participant::class,
                [
                    //'label' => 'motDePasse',
                    'mapped' => false,
                ]
            )*/
            //->add('administrateur')
            //->add('actif')
            ->add('pseudo')
            ->add('sortiesInscrit')
            ->add('getEstRattacheA', EntityType::class, [
                'class'=>Campus::class,
                'choice_label'=>'nom',
                'placeholder'=>'saisir un campus',
                'query_builder'=>function(CampusRepository $campusRepository)
                {
                    return $campusRepository->createQueryBuilder('c')->orderBy('c.nom','ASC');
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
