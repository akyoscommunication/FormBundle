<?php

namespace Akyos\FormBundle\Form;

use Akyos\FormBundle\Entity\ContactFormSubmissionValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormSubmissionValueType extends AbstractType
{
    private $contactFormSubmissionValue;

    public function __construct(ContactFormSubmissionValue  $contactFormSubmissionValue) {
        $this->contactFormSubmissionValue = $contactFormSubmissionValue;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {

            $event->getForm()
                ->add('value', TextType::class, [
                    'label' => $event->getData()->getContactFormField()->getTitle()
                ])
            ;
        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactFormSubmissionValue::class,
        ]);
    }
}
