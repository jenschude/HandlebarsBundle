<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Tests;

use JaySDe\HandlebarsBundle\Cache\Filesystem;
use JaySDe\HandlebarsBundle\HandlebarsEngine;
use JaySDe\HandlebarsBundle\HandlebarsEnvironment;
use JaySDe\HandlebarsBundle\HandlebarsHelperService;
use JaySDe\HandlebarsBundle\HandlebarsProfileExtension;
use JaySDe\HandlebarsBundle\Loader\FilesystemLoader;
use JaySDe\HandlebarsBundle\Extension\HandlebarsTwigExtension;
use JaySDe\HandlebarsBundle\Tests\Fixtures\TestBundle\TestBundle;
use LightnCandy\LightnCandy;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Templating\TemplateNameParser;

class FunctionalTest extends TestCase
{
    public function testTwigExtension()
    {
        $locator = new FileLocator();
        $templateLocator = new TemplateLocator($locator);
        $parser = new TemplateNameParser();
        $loader = new FilesystemLoader($templateLocator, $parser);
        $loader->addPath(__DIR__ . '/Fixtures/Resources/views');
        $this->addPaths($loader);

        $helperService = new HandlebarsHelperService();

        $cacheDir = __DIR__ . '/Fixtures/cache';
        $cache = new Filesystem(__DIR__ . '/Fixtures/cache');

        $profile = new \Twig_Profiler_Profile();
        $profiler = new HandlebarsProfileExtension($profile);
        $environment = new HandlebarsEnvironment(
            $loader,
            $helperService,
            ['flags' => LightnCandy::FLAG_BESTPERFORMANCE | LightnCandy::FLAG_ERROR_EXCEPTION],
            $cache,
            $profiler
        );
        $engine = new HandlebarsEngine($environment, $parser);


        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/Fixtures/Resources/views');
        $twig = new \Twig_Environment($loader);
        $twig->addExtension(new HandlebarsTwigExtension($engine));
        $result = $twig->render('test.html.twig');

        $this->assertSame('Hello world!', trim($result));

        array_map('unlink', glob("$cacheDir/**/*.*"));
        array_map('rmdir', glob("$cacheDir/**"));
        $this->assertTrue(rmdir($cacheDir));
    }

    public function testRender()
    {
        $locator = new FileLocator();
        $templateLocator = new TemplateLocator($locator);
        $parser = new TemplateNameParser();
        $loader = new FilesystemLoader($templateLocator, $parser);
        $loader->addPath(__DIR__ . '/Fixtures/Resources/views');
        $this->addPaths($loader);

        $helperService = new HandlebarsHelperService();

        $cacheDir = __DIR__ . '/Fixtures/cache';
        $cache = new Filesystem(__DIR__ . '/Fixtures/cache');

        $profile = new \Twig_Profiler_Profile();
        $profiler = new HandlebarsProfileExtension($profile);
        $environment = new HandlebarsEnvironment(
            $loader,
            $helperService,
            ['flags' => LightnCandy::FLAG_BESTPERFORMANCE | LightnCandy::FLAG_ERROR_EXCEPTION],
            $cache,
            $profiler
        );

        $engine = new HandlebarsEngine($environment, $parser);

        $result = $engine->render('main.hbs');
        $this->assertSame('Hello world!', trim($result));

        array_map('unlink', glob("$cacheDir/**/*.*"));
        array_map('rmdir', glob("$cacheDir/**"));
        $this->assertTrue(rmdir($cacheDir));
    }

    private function addPaths(FilesystemLoader $loader)
    {
        // register bundles as Handlebars namespaces
        foreach (['TestBundle' => TestBundle::class] as $bundle => $class) {
            $dir = __DIR__.'/Fixtures/Resources/'.$bundle.'/views';
            if (is_dir($dir)) {
                $loader->addPath(
                    $dir,
                    $this->normalizeBundleNameForLoaderNamespace($bundle)
                );
            }
            $reflection = new \ReflectionClass($class);
            $dir = dirname($reflection->getFileName()).'/Resources/views';
            if (is_dir($dir)) {
                $loader->addPath(
                    $dir,
                    $this->normalizeBundleNameForLoaderNamespace($bundle)
                );
            }
        }
    }

    private function normalizeBundleNameForLoaderNamespace($bundle)
    {
        if ('Bundle' === substr($bundle, -6)) {
            $bundle = substr($bundle, 0, -6);
        }

        return $bundle;
    }
}
