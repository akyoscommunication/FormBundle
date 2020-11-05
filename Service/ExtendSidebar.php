<?php

namespace Akyos\FormBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExtendSidebar
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getTemplate($route)
    {
        $template = '<li class="'.(strpos($route,"contact_form") !== false && strpos($route,"contact_form_submission") === false ? "active" : "").'"><a href="'.$this->router->generate('contact_form_index').'">Formulaire de contact</a></li>';
        $template .= '<li class="'.(strpos($route,"contact_form_submission") !== false ? "active" : "").'"><a href="'.$this->router->generate('contact_form_submission_index').'">Formulaires envoyés</a></li>';
        return new Response($template);
    }
}