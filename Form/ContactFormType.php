<?php

namespace Akyos\FormBundle\Form;

use Akyos\FormBundle\Entity\ContactForm;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'    => 'Titre du formulaire'
            ])
            ->add('formTo', TextType::class, [
                'label'    => 'Email de destination',
                'help'    => 'Séparer par une virgule, sans espace entre, pour mettre plusieurs emails.'
            ])
            ->add('formObject', TextType::class, [
                'label'    => 'Objet'
            ])
            ->add('mail', CKEditorType::class, [
                'required'    => false,
                'config'      => array(
                    'placeholder'    => "Texte",
                    'height'         => 50,
                    'entities'       => false,
                    'basicEntities'  => false,
                    'entities_greek' => false,
                    'entities_latin' => false,
                ),
                'label'    => 'Email'
            ])
            ->add('template', null, [
                'label'    => 'Template du mail',
            ])
            ->add('formTemplate', null, [
                'label'    => 'Template du formulaire',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactForm::class,
        ]);
    }
}
