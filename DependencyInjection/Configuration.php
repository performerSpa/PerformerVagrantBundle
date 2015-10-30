<?php

namespace Performer\VagrantBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $builder->root('performer_vagrant')
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('remote_php_interpreter')->defaultValue('/usr/bin/php')->end()
                        ->scalarNode('remote_site_dir')->defaultValue('/var/www')->end()
                        ->scalarNode('remote_symfony_console')->defaultValue('/bin/console')->end()
                    ->end()
                ->end()
                ->arrayNode('users')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('remote_commands')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $builder;
    }
}
