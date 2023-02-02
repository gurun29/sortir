<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreationSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteOrganisateur', EntityType::class, [    'class'=>Campus::class,    'label'=>'Campus',
                'choice_label'=>'nom',    'placeholder'=>'saisir un campus',
                'query_builder'=>function(CampusRepository $campusRepository)
                {        return $campusRepository->createQueryBuilder('c')->orderBy('c.nom','ASC');}])


            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :',
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'widget' => 'single_text',
                'html5' => true
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite d\'inscription :',
                'widget' => 'single_text',
                'html5' => true
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places :',
            ])
            ->add('duree', TextType::class, [
                'label' => 'Durée :',
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos :',
            ])
            #->add('etat_sortie')
            #->add('url_photo')


            ->add('lieu', EntityType::class, [    'class'=>lieu::class,    'label'=>'lieu',
                'choice_label'=>'nom',
                'query_builder'=>function(lieuRepository $lieuRepository)
                {        return $lieuRepository->createQueryBuilder('l')->orderBy('l.nom','ASC');}])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}