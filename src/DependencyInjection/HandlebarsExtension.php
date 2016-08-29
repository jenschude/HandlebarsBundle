<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\DependencyInjection;

use LightnCandy\LightnCandy;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class HandlebarsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('handlebars.xml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $config['flags'] = $this->getFlags($config);


        foreach ($config['translation'] as $key => $value) {
            $container->setParameter('handlebars.translation.'.$key, $value);
        }
        foreach ($config['cms'] as $key => $value) {
            $container->setParameter('handlebars.cms.'.$key, $value);
        }

        $this->setupAssetic($loader, $config, $container);
        $this->configurePath($config, $container);

        $container->getDefinition('handlebars.cache')->replaceArgument(0, $config['cache']);
        $container->getDefinition('handlebars.cache')->replaceArgument(1, $config['debug']);

        $container->getDefinition('handlebars')->replaceArgument(2, $config);
    }

    private function getFlags($config) {
        $flags = 0;
        if (isset($config['flags'])) {
            foreach ($config['flags'] as $flag) {
                $flags = $flags | constant('LightnCandy\LightnCandy::'.$flag);
            }
        }
        if (isset($config['excludeFlags'])) {
            foreach ($config['excludeFlags'] as $flag) {
                $flags = $flags & ~constant('LightnCandy\LightnCandy::'.$flag);
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

        return $flags;
    }

    private function setupAssetic(LoaderInterface $loader, $config, ContainerBuilder $container)
    {
        // Enable AsseticExtension if undefined
        if (!isset($config['assetic'])) {
            $config['assetic'] = array_key_exists('AsseticBundle', $container->getParameter('kernel.bundles'));
        }
        // Assetic Extension
        if (true === $config['assetic']) {
            $loader->load('assetic.xml');
        }
        $container->setParameter('handlebars.assetic', $config['assetic']);

    }

    private function configurePath($config, ContainerBuilder $container)
    {
        $this->addConfigPath($config, $container);
        $this->addContainerPath($container);
    }

    private function addContainerPath(ContainerBuilder $container)
    {
        $handlebarsFilesystemLoaderDefinition = $container->getDefinition('handlebars.loader.filesystem');

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
    }

    private function addConfigPath($config, ContainerBuilder $container)
    {
        $handlebarsFilesystemLoaderDefinition = $container->getDefinition('handlebars.loader.filesystem');

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
    }
}
