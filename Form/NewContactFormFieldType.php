<?php

namespace Akyos\FormBundle\Form;

use Akyos\FormBundle\Entity\ContactFormField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewContactFormFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre du champ',
                'help' => '( Votre titre )',
            ])
//            ->add('position', IntegerType::class, [
//                'label' => 'Position du champ',
//            ])
            ->add('slug', null, [
                'label' => 'Slug du champ',
            ])
            ->add('options', TextareaType::class, [
                'label' => 'Options du champs ( placeholder )',
                'help' => '( si le champs est un select, entrez vos choix dans l\'ordre séparé par des pipes ( | ). Le premier choix étant le placeholder )',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => array(
                    'Texte simple ( HTMl, utiliser le champs options )' => 'html',
                    'Champ Texte' => 'text',
                    'Zone de texte' => 'textarea',
                    'Nombre' => 'nb',
                    'Choix simple' => 'choice',
                    'Choix multiple' => 'multiple_choice',
                    'Téléphone' => 'tel',
                    'Email' => 'mail',
                    'Case à cocher' => 'checkbox',
                    'Hidden' => 'hidden',
                    'Fichier' => 'file'
                ),
                'label' => 'Type du champ',
            ])
            ->add('col', null, [
                'label' => 'Nombre de colonne',
                'help' => '( de 1 à 12 )',
                'attr' => [
                    'min' => '1',
                    'max' => '12',
                ]
            ])
            ->add('excludeRegex', TextType::class, [
                'label' => "Regex: exclusion",
                'help' => "Mettres les mots/bout de mots séparé par un |. Si le champs content un de ces mots, n'envoie pas le mail.",
            ])
            ->add('isRequired', null, [
                'label' => 'Le champ est-il requis ?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactFormField::class,
        ]);
    }
}
