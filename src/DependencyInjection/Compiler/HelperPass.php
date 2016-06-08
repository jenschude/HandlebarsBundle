<?php

namespace JaySDe\HandlebarsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HelperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('handlebars.helper')) {
            return;
        }

        $definition = $container->findDefinition(
            'handlebars.helper'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'handlebars.helper'
        );
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                $definition->addMethodCall(
                    'addHelper',
                    array($tag['id'], new Reference($id))
                );
            }
        }
    }
}
