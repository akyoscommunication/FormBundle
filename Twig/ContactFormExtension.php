<?php

namespace Akyos\FormBundle\Twig;

use Akyos\FormBundle\Controller\ContactFormFieldController;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ContactFormExtension extends AbstractExtension
{
    protected $form;
    public function __construct(ContactFormFieldController $form)
    {
        $this->form = $form;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderContactForm', [$this, 'renderContactForm']),
        ];
    }

    public function renderContactForm($idForm, $dynamicValues = [], $labels = true, $button_label = 'Envoyer', $object = null, $to = null, $formName = 'contactForm')
    {
        return $this->form->renderContactForm($idForm, $dynamicValues, $labels, $button_label, $object, $to, $formName)->getContent();
    }
}
