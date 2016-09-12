<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\DependencyInjection;


use JaySDe\HandlebarsBundle\DependencyInjection\HandlebarsExtension;
use LightnCandy\LightnCandy;
use Prophecy\Argument;
use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\Config\Resource\FileResource;

class HandlebarsExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $defaultFlags = $this->getDefaultFlags();

        $bundleDir = realpath(__DIR__ . '/../..');
        $kernelDir = $bundleDir . '/Tests/Fixtures';
        $resourceDir = $kernelDir . '/Resources/views';

        $parameterBag = $this->prophesize('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $loaderDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $loaderDefinition->addMethodCall("addPath", [$resourceDir])->shouldBeCalled();

        $container->getParameterBag()->willReturn($parameterBag->reveal());
        $container->hasExtension(Argument::any())->willReturn(false);
        $container->addResource(new FileResource(realpath(__DIR__ . '/../../Resources/config/handlebars.xml')))
            ->shouldBeCalled();
        $container->setDefinition(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setAlias(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Alias')
        )->shouldBeCalled();
        $container->setParameter("handlebars.cms.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_prefix", '%')->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_suffix", '%')->shouldBeCalled();
        $container->getParameter('kernel.bundles')->willReturn([])->shouldBeCalled();
        $container->setParameter('handlebars.assetic', false)->shouldBeCalled();

        $container->getDefinition("handlebars.loader.filesystem")
            ->willReturn($loaderDefinition->reveal())->shouldBeCalled();
        $container->getParameter("kernel.root_dir")->willReturn($kernelDir);
        $container->addResource(new FileExistenceResource($resourceDir))->shouldBeCalled();

        $cacheDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $cacheDefinition->replaceArgument(0, '%kernel.cache_dir%/handlebars')->shouldBeCalled();
        $cacheDefinition->replaceArgument(1, '%kernel.debug%')->shouldBeCalled();


        $hbDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $hbDefinition->replaceArgument(
            2,
            Argument::withEntry('flags', $defaultFlags)
        )->shouldBeCalled();

        $container->getDefinition("handlebars.cache")->willReturn($cacheDefinition);
        $container->getDefinition("handlebars")->willReturn($hbDefinition);


        $extension = new HandlebarsExtension();
        $extension->load([], $container->reveal());
    }

    public function testAssetic()
    {
        $bundleDir = realpath(__DIR__ . '/../..');
        $kernelDir = $bundleDir . '/Tests/Fixtures';
        $resourceDir = $kernelDir . '/Resources/views';

        $parameterBag = $this->prophesize('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $loaderDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $loaderDefinition->addMethodCall("addPath", [$resourceDir])->shouldBeCalled();

        $container->getParameterBag()->willReturn($parameterBag->reveal());
        $container->hasExtension(Argument::any())->willReturn(false);
        $container->addResource(new FileResource(realpath(__DIR__ . '/../../Resources/config/handlebars.xml')))
            ->shouldBeCalled();
        $container->setDefinition(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setAlias(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Alias')
        )->shouldBeCalled();
        $container->setParameter("handlebars.cms.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_prefix", '%')->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_suffix", '%')->shouldBeCalled();
        $container->getParameter('kernel.bundles')->willReturn([])->shouldBeCalled();
        $container->setParameter('handlebars.assetic', true)->shouldBeCalled();
        $container->addResource(new FileResource($bundleDir . '/Resources/config/assetic.xml'))->shouldBeCalled();

        $container->getDefinition("handlebars.loader.filesystem")
            ->willReturn($loaderDefinition->reveal())->shouldBeCalled();
        $container->getParameter("kernel.root_dir")->willReturn($kernelDir);
        $container->addResource(new FileExistenceResource($resourceDir))->shouldBeCalled();

        $cacheDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $cacheDefinition->replaceArgument(0, '%kernel.cache_dir%/handlebars')->shouldBeCalled();
        $cacheDefinition->replaceArgument(1, '%kernel.debug%')->shouldBeCalled();


        $hbDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $hbDefinition->replaceArgument(2, Argument::type('array'))->shouldBeCalled();

        $container->getDefinition("handlebars.cache")->willReturn($cacheDefinition);
        $container->getDefinition("handlebars")->willReturn($hbDefinition);


        $extension = new HandlebarsExtension();
        $extension->load([['assetic' => true]], $container->reveal());
    }

    public function testBundlePaths()
    {
        $bundleDir = realpath(__DIR__ . '/../..');
        $kernelDir = $bundleDir . '/Tests/Fixtures';
        $resourceDir = $kernelDir . '/Resources/views';

        $parameterBag = $this->prophesize('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $loaderDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $loaderDefinition->addMethodCall("addPath", [$resourceDir])->shouldBeCalled();
        $loaderDefinition->addMethodCall("addPath", [$kernelDir . '/Resources/TestBundle/views'])->shouldBeCalled();
        $loaderDefinition->addMethodCall("addPath", [$kernelDir . '/TestBundle/Resources/views'])->shouldBeCalled();
        $loaderDefinition->addMethodCall("addPath", [$kernelDir . '/Resources/TestBundle/views', 'Test'])->shouldBeCalled();
        $loaderDefinition->addMethodCall("addPath", [$kernelDir . '/TestBundle/Resources/views', 'Test'])->shouldBeCalled();

        $container->getParameterBag()->willReturn($parameterBag->reveal());
        $container->hasExtension(Argument::any())->willReturn(false);
        $container->addResource(new FileResource(realpath(__DIR__ . '/../../Resources/config/handlebars.xml')))
            ->shouldBeCalled();
        $container->setDefinition(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setAlias(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Alias')
        )->shouldBeCalled();
        $container->setParameter("handlebars.cms.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_prefix", '%')->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_suffix", '%')->shouldBeCalled();
        $container->getParameter('kernel.bundles')->willReturn(
            ['TestBundle' => 'JaySDe\HandlebarsBundle\Tests\Fixtures\TestBundle\TestBundle']
        )->shouldBeCalled();
        $container->setParameter('handlebars.assetic', false)->shouldBeCalled();

        $container->getDefinition("handlebars.loader.filesystem")
            ->willReturn($loaderDefinition->reveal())->shouldBeCalled();
        $container->getParameter("kernel.root_dir")->willReturn($kernelDir);
        $container->addResource(new FileExistenceResource($resourceDir))->shouldBeCalled();
        $container->addResource(new FileExistenceResource($kernelDir . '/Resources/TestBundle/views'))->shouldBeCalled();
        $container->addResource(new FileExistenceResource($kernelDir . '/TestBundle/Resources/views'))->shouldBeCalled();

        $cacheDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $cacheDefinition->replaceArgument(0, '%kernel.cache_dir%/handlebars')->shouldBeCalled();
        $cacheDefinition->replaceArgument(1, '%kernel.debug%')->shouldBeCalled();


        $hbDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $hbDefinition->replaceArgument(2, Argument::type('array'))->shouldBeCalled();

        $container->getDefinition("handlebars.cache")->willReturn($cacheDefinition);
        $container->getDefinition("handlebars")->willReturn($hbDefinition);


        $extension = new HandlebarsExtension();
        $extension->load([], $container->reveal());
    }

    public function testAddConfigPath()
    {
        $bundleDir = realpath(__DIR__ . '/../..');
        $kernelDir = $bundleDir . '/Tests/Fixtures';
        $resourceDir = $kernelDir . '/Resources/views';

        $parameterBag = $this->prophesize('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $loaderDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $loaderDefinition->addMethodCall("addPath", [$resourceDir])->shouldBeCalled();
        $loaderDefinition->addMethodCall("addPath", ["%kernel.root_dir%/Resources/views"])->shouldBeCalled();

        $container->getParameterBag()->willReturn($parameterBag->reveal());
        $container->hasExtension(Argument::any())->willReturn(false);
        $container->addResource(new FileResource(realpath(__DIR__ . '/../../Resources/config/handlebars.xml')))
            ->shouldBeCalled();
        $container->setDefinition(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setAlias(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Alias')
        )->shouldBeCalled();
        $container->setParameter("handlebars.cms.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_prefix", '%')->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_suffix", '%')->shouldBeCalled();
        $container->getParameter('kernel.bundles')->willReturn([])->shouldBeCalled();
        $container->setParameter('handlebars.assetic', false)->shouldBeCalled();

        $container->getDefinition("handlebars.loader.filesystem")
            ->willReturn($loaderDefinition->reveal())->shouldBeCalled();
        $container->getParameter("kernel.root_dir")->willReturn($kernelDir);
        $container->addResource(new FileExistenceResource($resourceDir))->shouldBeCalled();

        $cacheDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $cacheDefinition->replaceArgument(0, '%kernel.cache_dir%/handlebars')->shouldBeCalled();
        $cacheDefinition->replaceArgument(1, '%kernel.debug%')->shouldBeCalled();


        $hbDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $hbDefinition->replaceArgument(2, Argument::type('array'))->shouldBeCalled();

        $container->getDefinition("handlebars.cache")->willReturn($cacheDefinition);
        $container->getDefinition("handlebars")->willReturn($hbDefinition);


        $extension = new HandlebarsExtension();
        $extension->load([['paths' => ['%kernel.root_dir%/Resources/views']]], $container->reveal());
    }

    public function testFlags()
    {
        $defaultFlags = $this->getDefaultFlags();

        $bundleDir = realpath(__DIR__ . '/../..');
        $kernelDir = $bundleDir . '/Tests/Fixtures';
        $resourceDir = $kernelDir . '/Resources/views';

        $parameterBag = $this->prophesize('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $loaderDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $loaderDefinition->addMethodCall("addPath", [$resourceDir])->shouldBeCalled();

        $container->getParameterBag()->willReturn($parameterBag->reveal());
        $container->hasExtension(Argument::any())->willReturn(false);
        $container->addResource(new FileResource(realpath(__DIR__ . '/../../Resources/config/handlebars.xml')))
            ->shouldBeCalled();
        $container->setDefinition(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setAlias(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Alias')
        )->shouldBeCalled();
        $container->setParameter("handlebars.cms.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_prefix", '%')->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_suffix", '%')->shouldBeCalled();
        $container->getParameter('kernel.bundles')->willReturn([])->shouldBeCalled();
        $container->setParameter('handlebars.assetic', false)->shouldBeCalled();

        $container->getDefinition("handlebars.loader.filesystem")
            ->willReturn($loaderDefinition->reveal())->shouldBeCalled();
        $container->getParameter("kernel.root_dir")->willReturn($kernelDir);
        $container->addResource(new FileExistenceResource($resourceDir))->shouldBeCalled();

        $cacheDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $cacheDefinition->replaceArgument(0, '%kernel.cache_dir%/handlebars')->shouldBeCalled();
        $cacheDefinition->replaceArgument(1, '%kernel.debug%')->shouldBeCalled();


        $hbDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $hbDefinition->replaceArgument(
            2,
            Argument::withEntry('flags', $defaultFlags | LightnCandy::FLAG_INSTANCE)
        )->shouldBeCalled();

        $container->getDefinition("handlebars.cache")->willReturn($cacheDefinition);
        $container->getDefinition("handlebars")->willReturn($hbDefinition);


        $extension = new HandlebarsExtension();
        $extension->load([['flags' => ['FLAG_INSTANCE']]], $container->reveal());
    }

    private function getDefaultFlags()
    {
        return (LightnCandy::FLAG_BESTPERFORMANCE |
                LightnCandy::FLAG_HANDLEBARSJS |
                LightnCandy::FLAG_RUNTIMEPARTIAL |
                LightnCandy::FLAG_HANDLEBARSLAMBDA |
                LightnCandy::FLAG_EXTHELPER |
                LightnCandy::FLAG_ERROR_EXCEPTION) & ~LightnCandy::FLAG_STANDALONEPHP;
    }
    public function testExcludeFlags()
    {
        $defaultFlags = $this->getDefaultFlags();

        $bundleDir = realpath(__DIR__ . '/../..');
        $kernelDir = $bundleDir . '/Tests/Fixtures';
        $resourceDir = $kernelDir . '/Resources/views';

        $parameterBag = $this->prophesize('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $loaderDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $loaderDefinition->addMethodCall("addPath", [$resourceDir])->shouldBeCalled();

        $container->getParameterBag()->willReturn($parameterBag->reveal());
        $container->hasExtension(Argument::any())->willReturn(false);
        $container->addResource(new FileResource(realpath(__DIR__ . '/../../Resources/config/handlebars.xml')))
            ->shouldBeCalled();
        $container->setDefinition(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )->shouldBeCalled();
        $container->setAlias(
            Argument::type('string'),
            Argument::type('Symfony\Component\DependencyInjection\Alias')
        )->shouldBeCalled();
        $container->setParameter("handlebars.cms.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.default_namespace", null)->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_prefix", '%')->shouldBeCalled();
        $container->setParameter("handlebars.translation.interpolation_suffix", '%')->shouldBeCalled();
        $container->getParameter('kernel.bundles')->willReturn([])->shouldBeCalled();
        $container->setParameter('handlebars.assetic', false)->shouldBeCalled();

        $container->getDefinition("handlebars.loader.filesystem")
            ->willReturn($loaderDefinition->reveal())->shouldBeCalled();
        $container->getParameter("kernel.root_dir")->willReturn($kernelDir);
        $container->addResource(new FileExistenceResource($resourceDir))->shouldBeCalled();

        $cacheDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $cacheDefinition->replaceArgument(0, '%kernel.cache_dir%/handlebars')->shouldBeCalled();
        $cacheDefinition->replaceArgument(1, '%kernel.debug%')->shouldBeCalled();


        $hbDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $hbDefinition->replaceArgument(
            2,
            Argument::withEntry('flags', $defaultFlags & ~LightnCandy::FLAG_ERROR_LOG)
        )->shouldBeCalled();

        $container->getDefinition("handlebars.cache")->willReturn($cacheDefinition);
        $container->getDefinition("handlebars")->willReturn($hbDefinition);


        $extension = new HandlebarsExtension();
        $extension->load(
            [[
                'flags' => ['FLAG_STANDALONEPHP'],
                'excludeFlags' => ['FLAG_ERROR_LOG', 'FLAG_BESTPERFORMANCE']
            ]],
            $container->reveal()
        );
    }
}
