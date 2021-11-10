<?php

namespace Akyos\FormBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// TODO => est-ce que c'est utilisé quelque part ce truc ?
class Form extends AbstractController
{
    public function getTab(): string
    {
        $tab = '<li class="nav-item">';
            $tab .= '<a class="nav-link" id="builder-tab" data-toggle="tab" href="#builder" role="tab" aria-controls="builder" aria-selected="false">Builder</a>';
        $tab .= '</li>';
        return $tab;
    }

    public function getTabContent($objectType, $objectId): string
    {
        $em = $this->getDoctrine()->getManager();
        return '<div class="tab-pane fade" id="builder" role="tabpanel" aria-labelledby="builder-tab">...</div>';
    }
}