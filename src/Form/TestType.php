<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('choice3', ChoiceType::class,[
            'choices' => [
                'Main Statuses' => [
                    'Yes' => 'stock_yes',
                    'No' => 'stock_no',
                ],
                'Out of Stock Statuses' => [
                    'Backordered' => 'stock_backordered',
                    'Discontinued' => 'stock_discontinued',
                ]
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
