<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\DependencyInjection;

use LightnCandy\LightnCandy;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class HandlebarsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('handlebars.xml');

        $configuration = new Configuration();

        $handlebarsFilesystemLoaderDefinition = $container->getDefinition('handlebars.loader.filesystem');

        $config = $this->processConfiguration($configuration, $configs);

        $flags = 0;
        if (isset($config['flags'])) {
            foreach ($config['flags'] as $flag) {
                $flags = $flags | constant('LightnCandy\LightnCandy::' . $flag);
            }
        }
        if (isset($config['excludeFlags'])) {
            foreach ($config['excludeFlags'] as $flag) {
                $flags = $flags & ~constant('LightnCandy\LightnCandy::' . $flag);
            }
            unset($config['excludeFlags']);
        }
        // ensure base functionality with flag standalone disabled
        $flags = ($flags | LightnCandy::FLAG_BESTPERFORMANCE |
            LightnCandy::FLAG_HANDLEBARSJS |
            LightnCandy::FLAG_RUNTIMEPARTIAL |
            LightnCandy::FLAG_HANDLEBARSLAMBDA |
            LightnCandy::FLAG_EXTHELPER |
            LightnCandy::FLAG_ERROR_EXCEPTION) & ~LightnCandy::FLAG_STANDALONEPHP;

        $config['flags'] = $flags;

        // Enable AsseticExtension if undefined
        if (!isset($config['assetic'])) {
            $config['assetic'] = array_key_exists('AsseticBundle', $container->getParameter('kernel.bundles'));
        }
        // Assetic Extension
        if (true === $config['assetic']) {
            $loader->load('assetic.xml');
        }
        $container->setParameter('handlebars.assetic', $config['assetic']);

        foreach ($config['translation'] as $key => $value) {
            $container->setParameter('handlebars.translation.' . $key, $value);
        }

        // register user-configured paths
        foreach ($config['paths'] as $path => $namespace) {
            if (!$namespace) {
                $handlebarsFilesystemLoaderDefinition->addMethodCall('addPath', array($path));
            } else {
                $handlebarsFilesystemLoaderDefinition->addMethodCall('addPath', array($path, $namespace));
            }
        }
        $dir = $container->getParameter('kernel.root_dir').'/Resources/views';
        if (is_dir($dir)) {
            $handlebarsFilesystemLoaderDefinition->addMethodCall('addPath', array($dir));
        }
        $container->addResource(new FileExistenceResource($dir));
        
        // register bundles as Handlebars namespaces
        foreach ($container->getParameter('kernel.bundles') as $bundle => $class) {
            $dir = $container->getParameter('kernel.root_dir').'/Resources/'.$bundle.'/views';
            if (is_dir($dir)) {
                $handlebarsFilesystemLoaderDefinition->addMethodCall('addPath', array($dir));
            }
            $container->addResource(new FileExistenceResource($dir));

            $reflection = new \ReflectionClass($class);
            $dir = dirname($reflection->getFileName()).'/Resources/views';
            if (is_dir($dir)) {
                $handlebarsFilesystemLoaderDefinition->addMethodCall('addPath', array($dir));
            }
            $container->addResource(new FileExistenceResource($dir));
        }

        $container->getDefinition('handlebars.cache')->replaceArgument(0, $config['cache']);
        $container->getDefinition('handlebars.cache')->replaceArgument(1, $config['debug']);

        $container->getDefinition('handlebars')->replaceArgument(2, $config);
    }
}
