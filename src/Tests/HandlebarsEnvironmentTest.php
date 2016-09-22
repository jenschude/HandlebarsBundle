<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests;


use JaySDe\HandlebarsBundle\Cache\Filesystem;
use JaySDe\HandlebarsBundle\HandlebarsEnvironment;
use JaySDe\HandlebarsBundle\HandlebarsHelperService;
use Prophecy\Argument;
use Symfony\Component\Config\Resource\FileResource;

class HandlebarsEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!file_exists(__DIR__ . '/Fixtures/cache')) {
            mkdir(__DIR__ . '/Fixtures/cache');
        }
    }

    protected function tearDown()
    {
        array_map('unlink', glob(__DIR__ . '/Fixtures/cache/0d/*.*'));
        if (file_exists(__DIR__ . '/Fixtures/cache/0d')) {
            rmdir(__DIR__ . '/Fixtures/cache/0d');
        }
        if (file_exists(__DIR__ . '/Fixtures/cache')) {
            rmdir(__DIR__ . '/Fixtures/cache');
        }
    }

    public function testLoadTemplateAndCompileWithAutoReload()
    {
        $loader = $this->prophesize('JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $loader->getCacheKey('test')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');
        $loader->getSource('test')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');

        $helper = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsHelperService');

        $profiler = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsProfileExtension');

        $cache = $this->prophesize('JaySDe\HandlebarsBundle\Cache\Filesystem');

        $cache->generateKey('test')->willReturn('test');
        $cache->isFresh('test')->willReturn(false);
        $cache->write(
            'test',
            Argument::type('string'),
            Argument::allOf(
                Argument::containing(new FileResource(__DIR__ . '/Fixtures/Resources/views/main.hbs'))
            )
        )->shouldBeCalled();
        $cache->load('test')->shouldBeCalled();
        $environment = new HandlebarsEnvironment(
            $loader->reveal(),
            $helper->reveal(),
            [
                'auto_reload' => true,
            ],
            $cache->reveal(),
            $profiler->reveal()
        );


        $environment->loadTemplate('test');
    }

    public function testLoadTemplateAndCompileWithFixtures()
    {
        $loader = $this->prophesize('JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $loader->getCacheKey('main')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');
        $loader->getSource('main')->willReturn(file_get_contents(__DIR__ . '/Fixtures/Resources/views/main.hbs'));
        $loader->exists('@Test/exclamation.handlebars')->willReturn(false);
        $loader->exists('@Test/exclamation.hbs')->willReturn(true);
        $loader->exists('partial.hbs')->willReturn(false);
        $loader->exists('partial.handlebars')->willReturn(true);
        $loader->getCacheKey('@Test/exclamation.hbs')->willReturn(__DIR__ . '/Fixtures/Resources/TestBundle/views/exclamation.hbs');
        $loader->getCacheKey('partial.handlebars')->willReturn(__DIR__ . '/Fixtures/Resources/views/partial.handlebars');
        $loader->getSource('@Test/exclamation.hbs')->willReturn(file_get_contents(__DIR__ . '/Fixtures/Resources/TestBundle/views/exclamation.hbs'));
        $loader->getSource('partial.handlebars')->willReturn(file_get_contents(__DIR__ . '/Fixtures/Resources/views/partial.handlebars'));

        $helper = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsHelperService');

        $profiler = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsProfileExtension');

        $cache = $this->prophesize('JaySDe\HandlebarsBundle\Cache\Filesystem');

        $cache->generateKey('main')->willReturn('main');
        $cache->isFresh('main')->willReturn(false);
        $cache->write(
            'main',
            Argument::type('string'),
            Argument::allOf(
                Argument::containing(new FileResource(__DIR__ . '/Fixtures/Resources/views/main.hbs')),
                Argument::containing(new FileResource(__DIR__ . '/Fixtures/Resources/views/partial.handlebars'))
            )
        )->shouldBeCalled();
        $cache->load('main')->shouldBeCalled();

        $environment = new HandlebarsEnvironment(
            $loader->reveal(),
            $helper->reveal(),
            [
                'auto_reload' => true,
            ],
            $cache->reveal(),
            $profiler->reveal()
        );


        $environment->loadTemplate('main');
    }

    public function testLoadTemplateAndCompileWithoutAutoReload()
    {
        $loader = $this->prophesize('JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $loader->getCacheKey('test')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');
        $loader->getSource('test')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');

        $helper = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsHelperService');

        $profiler = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsProfileExtension');

        $cache = $this->prophesize('JaySDe\HandlebarsBundle\Cache\Filesystem');

        $cache->generateKey('test')->willReturn('test');
        $cache->write(
            'test',
            Argument::type('string'),
            Argument::allOf(
                Argument::containing(new FileResource(__DIR__ . '/Fixtures/Resources/views/main.hbs'))
            )
        )->shouldBeCalled();
        $cache->load('test')->shouldBeCalled();
        $environment = new HandlebarsEnvironment(
            $loader->reveal(),
            $helper->reveal(),
            [
                'auto_reload' => false,
            ],
            $cache->reveal(),
            $profiler->reveal()
        );


        $environment->loadTemplate('test');
    }

    public function testLoadTemplateFromCacheWithAutoReload()
    {
        $loader = $this->prophesize('JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $loader->getCacheKey('test')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');
        $loader->getSource('test')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');

        $helper = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsHelperService');

        $profiler = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsProfileExtension');

        $cache = $this->prophesize('JaySDe\HandlebarsBundle\Cache\Filesystem');

        $cache->generateKey('test')->willReturn('test');
        $cache->isFresh('test')->willReturn(true);
        $cache->load('test')->shouldBeCalled();
        $cache->write('test', Argument::any(), Argument::containing(Argument::type('Symfony\Component\Config\Resource\FileResource')))->shouldBeCalled();

        $environment = new HandlebarsEnvironment(
            $loader->reveal(),
            $helper->reveal(),
            [
                'auto_reload' => true,
            ],
            $cache->reveal(),
            $profiler->reveal()
        );


        $environment->loadTemplate('test');
    }

    public function testLoadTemplateFromCacheWithoutAutoReload()
    {
        $loader = $this->prophesize('JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $loader->getCacheKey('test')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');
        $loader->getSource('test')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');

        $helper = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsHelperService');

        $profiler = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsProfileExtension');

        $cache = $this->prophesize('JaySDe\HandlebarsBundle\Cache\Filesystem');

        $cache->generateKey('test')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');
        $cache->load(__DIR__ . '/Fixtures/Resources/views/main.hbs')->shouldBeCalled();

        $environment = new HandlebarsEnvironment(
            $loader->reveal(),
            $helper->reveal(),
            [
                'auto_reload' => false,
            ],
            $cache->reveal(),
            $profiler->reveal()
        );


        $environment->loadTemplate('test');
    }

    public function getFileNameData()
    {
        return [
            ['test', 'test'],
            [null, false],
            [false, false],
            ['', false],
        ];
    }
    /**
     * @dataProvider getFileNameData
     */
    public function testGetCacheFileNameFail($value, $expectedResult)
    {
        $loader = $this->prophesize('JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $helper = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsHelperService');
        $profiler = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsProfileExtension');
        $cache = $this->prophesize('JaySDe\HandlebarsBundle\Cache\Filesystem');
        $cache->generateKey('test')->willReturn($value)->shouldBeCalled();

        $environment = new HandlebarsEnvironment(
            $loader->reveal(),
            $helper->reveal(),
            [
                'auto_reload' => false,
            ],
            $cache->reveal(),
            $profiler->reveal()
        );

        $this->assertSame($expectedResult, $environment->getCacheFilename('test'));
    }

    public function testRender()
    {
        $loader = $this->prophesize('JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $helper = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsHelperService');

        $profiler = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsProfileExtension');
        $profiler->enter(Argument::type('Twig_Profiler_Profile'))->shouldBeCalled();
        $profiler->leave(Argument::type('Twig_Profiler_Profile'))->shouldBeCalled();

        $cache = $this->prophesize('JaySDe\HandlebarsBundle\Cache\Filesystem');
        $cache->generateKey('test')->willReturn('test');
        $cache->isFresh('test')->willReturn(true);
        $cache->load('test')->willReturn(function() { return 'hello world';});
        $cache->write('test', Argument::any(), Argument::containing(Argument::type('Symfony\Component\Config\Resource\FileResource')))->shouldBeCalled();

        $environment = new HandlebarsEnvironment(
            $loader->reveal(),
            $helper->reveal(),
            [
                'auto_reload' => true,
            ],
            $cache->reveal(),
            $profiler->reveal()
        );

        $this->assertSame('hello world', $environment->render('test', []));
    }

    public function testWithFixture()
    {
        $loader = $this->prophesize('JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $loader->getCacheKey('main')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');
        $loader->getSource('main')->willReturn(file_get_contents(__DIR__ . '/Fixtures/Resources/views/main.hbs'));
        $loader->exists('@Test/exclamation.handlebars')->willReturn(false);
        $loader->exists('@Test/exclamation.hbs')->willReturn(true);
        $loader->exists('partial.hbs')->willReturn(false);
        $loader->exists('partial.handlebars')->willReturn(true);
        $loader->getCacheKey('@Test/exclamation.hbs')->willReturn(__DIR__ . '/Fixtures/Resources/TestBundle/views/exclamation.hbs');
        $loader->getCacheKey('partial.handlebars')->willReturn(__DIR__ . '/Fixtures/Resources/views/partial.handlebars');
        $loader->getSource('@Test/exclamation.hbs')->willReturn(file_get_contents(__DIR__ . '/Fixtures/Resources/TestBundle/views/exclamation.hbs'));
        $loader->getSource('partial.handlebars')->willReturn(file_get_contents(__DIR__ . '/Fixtures/Resources/views/partial.handlebars'));

        $helper = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsHelperService');

        $profiler = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsProfileExtension');
        $profiler->enter(Argument::type('Twig_Profiler_Profile'))->shouldBeCalled();
        $profiler->leave(Argument::type('Twig_Profiler_Profile'))->shouldBeCalled();

        $cache = new Filesystem(__DIR__ . '/Fixtures/cache', true);

        $environment = new HandlebarsEnvironment(
            $loader->reveal(),
            $helper->reveal(),
            [
                'auto_reload' => true,
            ],
            $cache,
            $profiler->reveal()
        );

        $this->assertSame('Hello world!', trim($environment->render('main', [])));
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testCompileException()
    {
        $loader = $this->prophesize('JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $loader->getCacheKey('main')->willReturn(__DIR__ . '/Fixtures/Resources/views/main.hbs');
        $loader->getSource('main')->willReturn('{{>test }}');
        $loader->exists('test.handlebars')->willThrow(new \Exception());

        $helper = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsHelperService');

        $profiler = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsProfileExtension');

        $cache = $this->prophesize('JaySDe\HandlebarsBundle\Cache\Filesystem');

        $cache->generateKey('main')->willReturn('main');
        $environment = new HandlebarsEnvironment(
            $loader->reveal(),
            $helper->reveal(),
            [
                'auto_reload' => true,
            ],
            $cache->reveal(),
            $profiler->reveal()
        );

        $environment->compile('main');
    }
}
