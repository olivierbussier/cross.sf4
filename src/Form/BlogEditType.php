<?php

namespace App\Form;

use App\Classes\Blog\BlogHelpers;
use App\Entity\Blog;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre de l\'article',
                'attr' => [
                    'placeholder' => 'Entrez un titre pour l\'article'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu de l\'article',
                'config' => [
                    'uiColor' => '#ffffff'
                ]
            ])
/*            ->add('content', null, [
                'label' => 'Contenu de l\'article',
                'attr' => [
                    'placeholder' => 'Entrez le texte à afficher'
                ]
            ])*/
            ->add('link', null, [
                'label' => 'Lien vers un site',
                'attr' => [
                    'placeholder' => 'Lien ou aller quand on clicque sur l\'image'
                ]
            ])
            ->add('positionImage',ChoiceType::class,[
                'label' => 'Position de l\'image',
                'choices' => [
                    'Dessus'  => 'dessus' ,
                    'Dessous' => 'dessous',
                    'Gauche'  => 'gauche' ,
                    'Droite'  => 'droite'
                ]
            ])
            ->add('file', FileType::class, ['empty_data' => '', 'required' => false])
            ->add('Enregister', SubmitType::class, [
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
