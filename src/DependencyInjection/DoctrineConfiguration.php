<?php

namespace WND\SMVCF\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class DoctrineConfiguration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('doctrine');

        $rootNode
            ->children()
                ->scalarNode('mapping')->end()
                ->scalarNode('cache_dir')->end()
                ->arrayNode('config')
                    ->children()
                        ->scalarNode('driver')->end()
                        ->scalarNode('user')->end()
                        ->scalarNode('password')->end()
                        ->scalarNode('dbname')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
