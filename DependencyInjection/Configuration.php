<?php

namespace Akyos\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('form_bundle');

        $treeBuilder->getRootNode()->children()->scalarNode('contact_form_files_directory')->defaultValue('contact_form_files/')->end()->end()->end();

        return $treeBuilder;
    }
}
