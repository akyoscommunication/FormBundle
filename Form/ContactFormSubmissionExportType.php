<?php

namespace Akyos\FormBundle\Form;

use Akyos\FormBundle\Entity\ContactForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormSubmissionExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contactForm', EntityType::class, ['label' => 'Formulaire à exporter', 'class' => ContactForm::class, 'choice_label' => 'title',]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => null, 'label' => false,]);
    }
}
