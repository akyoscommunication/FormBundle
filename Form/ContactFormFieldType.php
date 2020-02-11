<?php

namespace Akyos\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormFieldType extends AbstractType
{
    protected $fields;
    protected $labels;
    protected $dynamicValues;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->fields = $options['fields'];
        $this->labels = $options['labels'];
        $this->dynamicValues = $options['dynamicValues'];

        foreach ($this->fields as $key => $field) {

            $title = $field->getTitle();
            $slug = $field->getSlug();
            $required = $field->getIsRequired();
            $col = $field->getCol();
            $opt = $field->getOptions();

            $value = array_key_exists($slug, $this->dynamicValues) ? $this->dynamicValues[$slug] : '';
            $placeholder = ($opt ? $opt.($required ? '*' : '') : $title.($required ? '*' : ''));
            $labels = ($this->labels ? $title : false );

            switch ($field->getType()) {

                case 'textarea':
                    $builder
                        ->add($slug, TextareaType::class, array(
                            'attr'              => array(
                                'placeholder'       => $placeholder,
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                            ),
                            'required'    => $required,
                            'label'    => $labels,
                        ))
                    ;
                    break;

                case 'tel':
                    $builder
                        ->add($slug, TelType::class, array(
                            'attr'              => array(
                                'placeholder'       => $placeholder,
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $labels,
                            'required'              => $required,
                        ))
                    ;
                    break;

                case 'mail':
                    $builder
                        ->add($slug, EmailType::class, array(
                            'attr'              => array(
                                'placeholder'       => $placeholder,
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $labels,
                            'required'              => $required,
                        ))
                    ;
                    break;

                case 'nb':
                    $builder
                        ->add($slug, IntegerType::class, array(
                            'attr'              => array(
                                'placeholder'       => $placeholder,
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $labels,
                            'required'              => $required,
                        ))
                    ;
                    break;

                case 'choice':
                    $fieldOptions = explode('|', $opt);

                    $builder
                        ->add($slug, ChoiceType::class, array(
                            'attr'              => array(
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                            ),
                            'choices' => array_slice($fieldOptions, 1),
                            'choice_label' => function ($choice, $key, $value) {
                                return $value;
                            },
                            'placeholder'       => $fieldOptions[0],
                            'block_prefix' => 'contactform',
                            'label'                 => $labels,
                            'required'              => $required,
                        ))
                    ;
                    break;

                case 'multiple_choice':
                    $fieldOptions = explode('|', $opt);

                    $builder
                        ->add($slug, ChoiceType::class, array(
                            'attr'              => array(
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                                'class' => 'form-control js-select2'
                            ),
                            'multiple' => true,
                            'choices' => array_slice($fieldOptions, 1),
                            'choice_label' => function ($choice, $key, $value) {
                                return $value;
                            },
                            'placeholder'       => $fieldOptions[0],
                            'block_prefix' => 'contactform',
                            'label'                 => $labels,
                            'required'              => $required,
                        ))
                    ;
                    break;

                case 'hidden':
                    $builder
                        ->add($slug, HiddenType::class, array(
                            'attr'              => array(
                                'placeholder'       => $placeholder,
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $labels,
                            'required'              => $required,
                        ))
                    ;
                    break;


                default:
                    $builder
                        ->add($slug, TextType::class, array(
                            'attr'              => array(
                                'placeholder'       => $placeholder,
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                            ),
                            'block_prefix' => 'contactform',
                            'label'                 => $labels,
                            'required'              => $required,
                        ))
                    ;
                    break;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'fields' => null,
            'dynamicValues' => [],
            'labels' => true,
            'allow_extra_fields' => true,
        ]);
    }
}
