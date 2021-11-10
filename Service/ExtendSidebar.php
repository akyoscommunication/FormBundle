<?php

namespace Akyos\FormBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class ExtendSidebar
{
    private UrlGeneratorInterface $router;
    private Security $security;

    public function __construct(UrlGeneratorInterface $router, Security $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    public function getTemplate($route): Response
    {
        $template ='';
        if($this->security->isGranted('formulaire-de-contact')){
            $template = '<li class="'.(strpos($route,"contact_form") !== false && strpos($route,"contact_form_submission") === false ? "active" : "").'"><a href="'.$this->router->generate('contact_form_index').'">Formulaire de contact</a></li>';
        }
        if($this->security->isGranted('formulaire-envoyés')){
            $template .= '<li class="'.(strpos($route,"contact_form_submission") !== false ? "active" : "").'"><a href="'.$this->router->generate('contact_form_submission_index').'">Formulaires envoyés</a></li>';
        }
        return new Response($template);
    }
}