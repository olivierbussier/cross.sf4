<?php

namespace App\Form;

use App\Entity\CrossConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrossConfigEditType extends AbstractType
{
    private $choices = [
        'choices' => [
            'Ouvert' => true,
            'Fermé'  => false
        ]
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateEdition', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('lienTrail', TextType::class)
            ->add('fTrail'   , ChoiceType::class, $this->choices)
            ->add('lienCross', TextType::class)
            ->add('fCross'   , ChoiceType::class, $this->choices)
            ->add('lienMarche', TextType::class)
            ->add('fMarche'   , ChoiceType::class, $this->choices)
            ->add('lienPetit', TextType::class)
            ->add('fPetit'   , ChoiceType::class, $this->choices)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CrossConfig::class,
        ]);
    }
}
