<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('handlebars')
            ->fixXmlConfig('path')
            ->children()
                ->booleanNode('assetic')->end()
                ->scalarNode('cache')->defaultValue('%kernel.cache_dir%/handlebars')->end()
                ->booleanNode('debug')->defaultValue('%kernel.debug%')->end()
                ->booleanNode('auto_reload')->defaultValue('%kernel.debug%')->end()
                ->arrayNode('paths')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('paths')
                    ->beforeNormalization()
                        ->always()
                        ->then(function ($paths) {
                            $normalized = array();
                            foreach ($paths as $path => $namespace) {
                                if (is_array($namespace)) {
                                    // xml
                                    $path = $namespace['value'];
                                    $namespace = $namespace['namespace'];
                                }

                                // path within the default namespace
                                if (ctype_digit((string) $path)) {
                                    $path = $namespace;
                                    $namespace = null;
                                }

                                $normalized[$path] = $namespace;
                            }

                            return $normalized;
                        })
                        ->end()
                    ->prototype('variable')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
