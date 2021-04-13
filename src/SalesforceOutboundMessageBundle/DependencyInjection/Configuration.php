<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('arsenal');
        $treeBuilder
            ->getRootNode()
            ->children()
                ->arrayNode('salesforce_outbound_message')->isRequired()
                    ->children()
                        ->scalarNode('wsdl_cache')
                            ->defaultValue('WSDL_CACHE_DISK')
                        ->end()
                        ->scalarNode('wsdl_directory')
                            ->isRequired()
                        ->end()
                        ->arrayNode('document_paths')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('path')
                                        ->isRequired()
                                    ->end()
                                    ->scalarNode('force_compare')
                                        ->defaultFalse()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}