<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Faculte;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mot', TextType::class, ['label' => 'Mot'])
            ->add('home', TextType::class, ['label' => 'Home'])
            ->add('faculte', EntityType::class, [
                'class' => Faculte::class,
                'choice_label' => 'nom',
                'label' => 'Faculte',
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Create'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Departement::class,
        ]);
    }
}
