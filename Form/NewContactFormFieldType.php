<?php

namespace Akyos\FormBundle\Form;

use Akyos\FormBundle\Entity\ContactFormField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('position', IntegerType::class, [
                'label' => 'Position du champ',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => array(
                    'Texte' => 'text',
                    'Zone de texte' => 'textarea',
                    'Image' => 'image',
                    'Lien interne' => 'pagelink',
                    'Lien externe' => 'link',
                    'Téléphone' => 'tel',
                    'Email' => 'mail',
                    'Hidden' => 'hidden'
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
