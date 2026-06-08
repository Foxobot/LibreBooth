<?php

namespace Photobooth\Configuration\Section;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class AdminPanelConfiguration
{
    public static function getNode(): NodeDefinition
    {
        return (new TreeBuilder('adminpanel'))->getRootNode()->addDefaultsIfNotSet()
            ->ignoreExtraKeys()
            ->children()
                ->enumNode('view')
                    ->values(['basic', 'advanced', 'expert', 'custom'])
                    ->defaultValue('basic')
                    ->end()
                ->booleanNode('experimental_settings')->defaultValue(false)->end()
				/**
                 * ------------------------------------
                 * Custom view whitelist
                 * ------------------------------------
                 * List of setting keys shown in custom view
                 */
                ->arrayNode('custom_settings')
                    ->scalarPrototype()->end()
                    ->defaultValue([])
                ->end()
            ->end();
    }
}
