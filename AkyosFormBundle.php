<?php

namespace Akyos\FormBundle;

use Akyos\FormBundle\DependencyInjection\FormBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AkyosFormBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new FormBundleExtension();
    }
}