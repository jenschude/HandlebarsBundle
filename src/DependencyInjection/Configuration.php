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
        $flags = array(
            'FLAG_ERROR_LOG',
            'FLAG_ERROR_EXCEPTION',
            'FLAG_JSTRUE',
            'FLAG_JSOBJECT',
            'FLAG_JSLENGTH',
            'FLAG_THIS',
            'FLAG_PARENT',
            'FLAG_HBESCAPE',
            'FLAG_ADVARNAME',
            'FLAG_SPACECTL',
            'FLAG_NAMEDARG',
            'FLAG_SPVARS',
            'FLAG_PREVENTINDENT',
            'FLAG_SLASH',
            'FLAG_ELSE',
            'FLAG_RAWBLOCK',
            'FLAG_HANDLEBARSLAMBDA',
            'FLAG_PARTIALNEWCONTEXT',
            'FLAG_IGNORESTANDALONE',
            'FLAG_STRINGPARAMS',
            'FLAG_KNOWNHELPERSONLY',
            'FLAG_STANDALONEPHP',
            'FLAG_EXTHELPER',
            'FLAG_ECHO',
            'FLAG_PROPERTY',
            'FLAG_METHOD',
            'FLAG_RUNTIMEPARTIAL',
            'FLAG_NOESCAPE',
            'FLAG_MUSTACHELOOKUP',
            'FLAG_ERROR_SKIPPARTIAL',
            'FLAG_MUSTACHELAMBDA',
            'FLAG_NOHBHELPERS',
            'FLAG_RENDER_DEBUG',
            'FLAG_BESTPERFORMANCE',
            'FLAG_JS',
            'FLAG_INSTANCE',
            'FLAG_MUSTACHE',
            'FLAG_HANDLEBARS',
            'FLAG_HANDLEBARSJS',
            'FLAG_HANDLEBARSJS_FULL',
        );
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('handlebars')
            ->fixXmlConfig('path')
            ->children()
                ->booleanNode('assetic')->end()
                ->scalarNode('cache')->defaultValue('%kernel.cache_dir%/handlebars')->end()
                ->booleanNode('debug')->defaultValue('%kernel.debug%')->end()
                ->booleanNode('auto_reload')->defaultValue('%kernel.debug%')->end()
                ->arrayNode('cms')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_namespace')->defaultValue(null)->end()
                    ->end()
                ->end()
                ->arrayNode('translation')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_namespace')->defaultValue(null)->end()
                        ->scalarNode('interpolation_prefix')->defaultValue('%')->end()
                        ->scalarNode('interpolation_suffix')->defaultValue('%')->end()
                    ->end()
                ->end()
                ->arrayNode('flags')
                    ->prototype('enum')
                        ->values($flags)
                    ->end()
                ->end()
                ->arrayNode('excludeFlags')
                    ->prototype('enum')
                        ->values($flags)
                    ->end()
                ->end()
                ->arrayNode('paths')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('paths')
                    ->beforeNormalization()
                        ->always()
                        ->then(function($paths) {
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
