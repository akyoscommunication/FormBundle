<?php

namespace Akyos\FormBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Form extends AbstractController
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getTab()
    {
        $tab = '<li class="nav-item">';
            $tab .= '<a class="nav-link" id="builder-tab" data-toggle="tab" href="#builder" role="tab" aria-controls="builder" aria-selected="false">Builder</a>';
        $tab .= '</li>';
        return $tab;
    }

    public function getTabContent($objectType, $objectId)
    {

        $em = $this->getDoctrine()->getManager();
        $components = $em->getRepository("Akyos\\FormBundle\\Entity\\Component")->findBy(["type" => $objectType, "typeId" => $objectId], []);

        $tabContent = '<div class="tab-pane fade" id="builder" role="tabpanel" aria-labelledby="builder-tab">...</div>';
        return $tabContent;
//        return $this->render('builder/render.html.twig', [
//            'components' => $components,
//        ]);
    }
}