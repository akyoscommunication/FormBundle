<?php

namespace Akyos\FormBundle\Form;

use Akyos\FormBundle\Entity\ContactFormField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormFieldType extends AbstractType
{
    protected $fields;
    protected $dynamicValues;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->fields = $options['fields'];
        $this->dynamicValues = $options['dynamicValues'];

        foreach ($this->fields as $key => $field) {
            switch ($field->getType()) {

                case 'textarea':
                    $builder
                        ->add($field->getSlug(), CKEditorType::class, array(
                            'attr'              => array(
                                'placeholder'       => $field->getTitle(),
                                'row_attr'    => 'col-md-'.$field->getCol(),
                                'value'    => array_key_exists($field->getSlug(), $this->dynamicValues) ? $this->dynamicValues[$field->getSlug()] : '',
                            ),
                            'required'    => false,
                            'config'      => array(
                                'placeholder'    => "Texte",
                                'height'         => 50,
                                'entities'       => false,
                                'basicEntities'  => false,
                                'entities_greek' => false,
                                'entities_latin' => false,
                            ),
                            'label'    => $field->getTitle(),
                        ))
                    ;
                    break;

                case 'tel':
                    $builder
                        ->add($field->getSlug(), TelType::class, array(
                            'attr'              => array(
                                'placeholder'       => "Numéro",
                                'row_attr'    => 'col-md-'.$field->getCol(),
                                'value'    => array_key_exists($field->getSlug(), $this->dynamicValues) ? $this->dynamicValues[$field->getSlug()] : '',
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $field->getTitle(),
                            'required'              => false,
                        ))
                    ;
                    break;

                case 'mail':
                    $builder
                        ->add($field->getSlug(), EmailType::class, array(
                            'attr'              => array(
                                'placeholder'       => "Email",
                                'row_attr'    => 'col-md-'.$field->getCol(),
                                'value'    => array_key_exists($field->getSlug(), $this->dynamicValues) ? $this->dynamicValues[$field->getSlug()] : '',
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $field->getTitle(),
                            'required'              => false,
                        ))
                    ;
                    break;

                case 'pagelink':
                    $builder
                        ->add($field->getSlug(), ChoiceType::class, array(
                            'attr'              => array(
                                'row_attr'    => 'col-md-'.$field->getCol(),
                                'value'    => array_key_exists($field->getSlug(), $this->dynamicValues) ? $this->dynamicValues[$field->getSlug()] : '',
                            ),
                            'choices' => $this->pages,
                            'label'  => $field->getTitle(),
                            'block_prefix' => 'contactform',
                        ))
                    ;
                    break;

                case 'link':
                    $builder
                        ->add($field->getSlug(), UrlType::class, array(
                            'attr'              => array(
                                'row_attr'    => 'col-md-'.$field->getCol(),
                                'value'    => array_key_exists($field->getSlug(), $this->dynamicValues) ? $this->dynamicValues[$field->getSlug()] : '',
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $field->getTitle(),
                            'required'              => false,
                        ))
                    ;
                    break;

                case 'hidden':
                    $builder
                        ->add($field->getSlug(), HiddenType::class, array(
                            'attr'              => array(
                                'placeholder'       => "Lien",
                                'row_attr'    => 'col-md-'.$field->getCol(),
                                'value'    => array_key_exists($field->getSlug(), $this->dynamicValues) ? $this->dynamicValues[$field->getSlug()] : '',
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $field->getTitle(),
                            'required'              => false,
                        ))
                    ;
                    break;


                default:
                    $builder
                        ->add($field->getSlug(), TextType::class, array(
                            'attr'              => array(
                                'placeholder'       => $field->getTitle(),
                                'row_attr'    => 'col-md-'.$field->getCol(),
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $field->getTitle(),
                            'required'              => false,
                        ))
                    ;
                    break;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactFormField::class,
            'fields' => null,
            'dynamicValues' => [],
        ]);
    }
}
