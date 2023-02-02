<?php

namespace App\Form;

use App\Entity\Sortie;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnulerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class)
            ->add('infosSortie',TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('motif', TextareaType::class,[
                'label'=>'motif'
            ])
            ->add('submit', SubmitType::class,[
                'label' =>'Annuler la sorite',
                'attr' => [
                    'class' => 'btn btn-danger w-100'
                ]
            ])

        ;
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
