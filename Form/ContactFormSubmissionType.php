<?php

namespace Akyos\FormBundle\Form;

use Akyos\FormBundle\Entity\ContactForm;
use Akyos\FormBundle\Entity\ContactFormSubmission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormSubmissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sentFrom', TextType::class, [
                'label' => 'Expéditeur'
            ])
            ->add('sentTo', TextType::class, [
                'label' => 'Destinataire'
            ])
            ->add('object', TextType::class, [
                'label' => 'Objet'
            ])
            ->add('contactFormSubmissionValues', CollectionType::class, [
                'label' => 'Informations saisies',
                'entry_type' => ContactFormSubmissionValueType::class,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactFormSubmission::class,
        ]);
    }
}
