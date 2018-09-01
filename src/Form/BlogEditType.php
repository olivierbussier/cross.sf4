<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogEditType extends AbstractType
{
    public const AUCUNE  = 0;
    public const DESSUS  = 1;
    public const DESSOUS = 2;
    public const GAUCHE  = 3;
    public const DROITE  = 4;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre de l\'article',
                'attr' => [
                    'placeholder' => 'Entrez un titre pour l\'article'
                ]
            ])
            ->add('content', null, [
                'label' => 'Contenu de l\'article',
                'attr' => [
                    'placeholder' => 'Entrez le texte à afficher'
                ]
            ])
            ->add('link', null, [
                'label' => 'Lien vers un site',
                'attr' => [
                    'placeholder' => 'Lien ou aller quand on clicque sur l\'image'
                ]
            ])
            ->add('positionImage',ChoiceType::class,[
                'label' => 'Position de l\'image',
                'choices' => [
                    'Dessus'  => self::DESSUS,
                    'Dessous' => self::DESSOUS,
                    'Gauche'  => self::GAUCHE,
                    'Droite'  => self::DROITE
                ]
            ])
            ->add('image', FileType::class, ['empty_data' => '', 'required' => false])
            ->add('Supprimer l\'image', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-default btn-block btn-danger '
                ]
            ])
            ->add('Enregister', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-default btn-block'
                ]
            ])
            ->add('Annuler', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-default btn-block'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
