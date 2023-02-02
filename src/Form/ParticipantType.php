<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Repository\CampusRepository;
use Doctrine\DBAL\Types\TextType;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Form\Extension\Core\Type\FileType;

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
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('telephone',null,
                [
                    'label' => 'N° de de téléphone',
                ]
            )
            ->add('mail', EmailType::class,
                [
                    //'method'=> 'email',
                    'label' => 'N° de de téléphone',
                ]
            )
            //->add('password')
            //->add('mdp',PasswordType::class,
            ->add('mdp',PasswordType::class,
                [
                    'label' => 'Mot de passe',
                    'mapped' => false,
                    'required'=> false,
                    //'always_empty'=> true,
                ]
            )
            ->add('mdp2',PasswordType::class,
                [
                    'label' => 'Confirmez le mot de passe',
                    'mapped' => false,
                    'required'=> false,
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

            //->add('sortiesInscrit')
            ->add('getEstRattacheA', EntityType::class, [
                'class'=>Campus::class,
                'label'=>'Campus',
                'choice_label'=>'nom',
                'placeholder'=>'saisir un campus',
                'query_builder'=>function(CampusRepository $campusRepository)
                {
                    return $campusRepository->createQueryBuilder('c')->orderBy('c.nom','ASC');
                    //return $campusRepository->createQueryBuilder('name')->orderBy('nom','ASC');
                }
            ])
            //->add('estRattacheA')
            ->add('images', FileType::class,[
                'label' => 'photo de profil',
                'multiple' => false,
                'mapped' => false,
                'required' => false
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
