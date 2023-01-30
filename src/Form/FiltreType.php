<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\filtres\Filtres;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class FiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('camp',EntityType::class,[
                'class' => Campus::class,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.nom', 'ASC');},
                 'required'  => false,
                'label'=>'campus'


            ])

            ->add('nomDeSortie',SearchType::class,[
                'label'=>'Le nom de la sortie contient',
                'required'=>false,
                'attr'=>[
                    'placeholder'=>'search'
                ]
            ])

            ->add('dateMin',DateType::class,[
                'label'=>'Entre',
                'html5'=>true,
                'widget'=>'single_text',
                'required'=>false,
            ])
            ->add('dateMax',DateType::class,[
                'label'=>'et',
                'html5'=>true,
                'widget'=>'single_text',
                'required'=>false,
            ])
            ->add('organisateur',CheckboxType::class,  [
                'label'    => 'Sorties dont je suis organisateur',
                'attr' => ['class' => 'form-check'],
                'required'      => false,

            ])
            ->add('inscrit',CheckboxType::class,  [
                'label'    => 'Sorties auxquelle je suis inscrit',
                'attr' => ['class' => 'form-check'],
                'required'      => false,

            ])
            ->add('nonInscrit',CheckboxType::class,  [
                'label'    => 'Sorties auxquelles je ne suis pas inscrit',
                'attr' => ['class' => 'form-check'],
                'required'      => false,

            ])
            ->add('sortiePasser',CheckboxType::class,  [
                'label'    => 'Sorties passées',
                'attr' => ['class' => 'form-check'],
                'required'      => false,


            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn btn-success w-100'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
        'data_class'=>Filtres::class,
        'method'=>'GET',
        'csrf_protection'=> false
]);
}
public function getBlockPrefix()
{
    return '';
}
}