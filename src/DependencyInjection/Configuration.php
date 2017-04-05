<?php

namespace Sokil\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('notification');

        $rootNode
            ->children()
                ->arrayNode('transport')
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('email')
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('from')
                                    ->children()
                                        ->scalarNode('address')->end()
                                        ->scalarNode('sender_name')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('schema')
                    ->prototype('array')
                        ->children()
                            ->integerNode('id')->isRequired()->end()
                            ->scalarNode('name')->isRequired()->end()
                            ->arrayNode('recipients')
                                ->isRequired()
                                ->children()
                                    // describe transports
                                    ->arrayNode('email')->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
