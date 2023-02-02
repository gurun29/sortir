<?php

namespace App\Form;

use App\Entity\Campus;
use App\filtres\FiltresCampus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class FiltreCampusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('nom')

            ->add('nomDeSortie',SearchType::class,[
                'label'=>'Le nom du campus contient',
                'required'=>false,
                'attr'=>[
                    'placeholder'=>'search'
                ]
            ])
//            ->add('submit', SubmitType::class, [
//                'label' => 'Rechercher',
//                'attr' => [
//                    'class' => 'btn btn-success w-100'
//                ]
//            ])

//            ->add('campus',EntityType::class,[
//                'class' => Campus::class,
//                'required'  => false,
//            ])

//            ->add('campus',EntityType::class,[
//                'class' => FiltresCampus::class,
//                'query_builder' => function(EntityRepository $repository) {
//                    return $repository->createQueryBuilder('c')->orderBy('c.nom', 'ASC');},
//                'required'  => false,
//                'label'=>'campus'
//
//
//            ])

     ;

    }

//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => Campus::class,
//        ]);
//    }

    public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
        'data_class'=>FiltresCampus::class,
        'method'=>'GET',
        'csrf_protection'=> false
]);
}
public function getBlockPrefix()
{
    return '';
}

}