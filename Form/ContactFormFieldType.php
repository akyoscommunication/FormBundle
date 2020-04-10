<?php

namespace Akyos\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

            $opt = (array_key_exists($slug, $this->dynamicValues) and isset($this->dynamicValues[$slug]['options'])) ? $this->dynamicValues[$slug]['options'] : $field->getOptions();

            $value = (array_key_exists($slug, $this->dynamicValues) and isset($this->dynamicValues[$slug]['value'])) ? $this->dynamicValues[$slug]['value'] : '';
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
                    $array = [];

                    $opt = explode('|', $opt);

                    $fieldOptions = array_slice($opt, 1);
                    foreach ($fieldOptions as $f) {
                        $fieldOptionsVal = explode(';', $f);
                        if (count($fieldOptionsVal) < 2) {
                            $array[$fieldOptionsVal[0]] = $fieldOptionsVal[0];
                        } else {
                            if ($fieldOptionsVal[1] === '') {
                                $array[$fieldOptionsVal[0]] = $fieldOptionsVal[0];
                            } else {
                                $array[$fieldOptionsVal[0]] = $fieldOptionsVal[1];
                            }
                        }
                    }

                    $builder
                        ->add($slug, ChoiceType::class, array(
                            'attr'              => array(
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                            ),
                            'choices' => $array,
                            'choice_value' => function ($value)  {
                                return $value;
                            },
                            'placeholder'       => $opt[0],
                            'block_prefix' => 'contactform',
                            'label'                 => $labels,
                            'required'              => $required,
                        ))
                    ;
                    break;

                case 'multiple_choice':
                    $array = [];

                    $opt = explode('|', $opt);

                    $fieldOptions = array_slice($opt, 1);
                    foreach ($fieldOptions as $f) {
                        $fieldOptionsVal = explode(';', $f);
                        if (count($fieldOptionsVal) < 2) {
                            $array[$fieldOptionsVal[0]] = $fieldOptionsVal[0];
                        } else {
                            if ($fieldOptionsVal[1] === '') {
                                $array[$fieldOptionsVal[0]] = $fieldOptionsVal[0];
                            } else {
                                $array[$fieldOptionsVal[0]] = $fieldOptionsVal[1];
                            }
                        }
                    }

                    $builder
                        ->add($slug, ChoiceType::class, array(
                            'attr'              => array(
                                'row_attr'    => 'col-md-'.$col,
                                'value'    => $value,
                                'class' => 'form-control js-select2'
                            ),
                            'multiple' => true,
                            'choices' => $array,
                            'choice_value' => function ($value) {
                                return $value;
                            },
                            'placeholder'       => $opt[0],
                            'block_prefix' => 'contactform',
                            'label'                 => $labels,
                            'required'              => $required,
                        ))
                    ;
                    break;

                case 'checkbox':
                    $builder
                        ->add($slug, CheckboxType::class, array(
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
