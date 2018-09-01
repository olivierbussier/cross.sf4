<?php

namespace App\Form;

use App\Entity\Resultat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisieResultatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $annees = [];
        $annees['Importer toutes les années présentes dans le fichier'] = 0;

        for ($i= 2000;$i<2099;$i++) {
            $annees["N'importer que l'année $i"] = $i;
        }

        $builder
            ->add('Fichier',FileType::class, [
                'label' => 'Fichier à charger'
            ])
            ->add('anneeCross',ChoiceType::class, [
                'choices' => $annees ,
                'label' => 'Année à ajouter'
            ])
            ->add('AjouteRésultats', SubmitType::class, [
                'label' => 'Ajouter les résultats',
                'attr' => [
                    'class' => 'btn-block btn-success'
                ]])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resultat::class,
        ]);
    }
}
