<?php
declare(strict_types=1);

namespace Arxy\GdprDumpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('arxy_gdpr_dump');
        $rootNode = $treeBuilder->getRootNode();

        $rootChildren = $rootNode
            ->children()
            ->scalarNode('dsn')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('value_converter')->end()
            ->arrayNode('dump_settings')->useAttributeAsKey('name')->variablePrototype()->end()->end()
            ->arrayNode('pdo_settings')->useAttributeAsKey('name')->variablePrototype()->end()->end();

        $this->gdpr($rootChildren);

        $rootNode->end();

        return $treeBuilder;
    }

    private function gdpr(NodeBuilder $rootNode)
    {
        return $rootNode
            ->arrayNode('gdpr')
            ->arrayPrototype()
            ->arrayPrototype()
            ->children()
            ->scalarNode('transformer')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('options')->useAttributeAsKey('name')->variablePrototype()->end()->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }
}
