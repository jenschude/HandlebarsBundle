<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Tests\Loader;

use JaySDe\HandlebarsBundle\Loader\FilesystemLoader;
use JaySDe\HandlebarsBundle\Tests\TestCase;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;

class FilesystemLoaderTest extends TestCase
{
    public function testGetSource()
    {
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnValue(__DIR__.'/../DependencyInjection/Fixtures/Resources/views/layout.html.hbs'))
        ;
        $loader = new FilesystemLoader($locator, $parser);
        $loader->addPath(__DIR__.'/../DependencyInjection/Fixtures/Resources/views', 'namespace');
        // Twig-style
        $this->assertEquals("This is a layout\n", $loader->getSource('@namespace/layout.html.hbs'));
        // Symfony-style
        $this->assertEquals("This is a layout\n", $loader->getSource('HandlebarsBundle::layout.html.hbs'));
    }

    public function testExists()
    {
        // should return true for templates that Handlebars does not find, but Symfony does
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnValue($template = __DIR__.'/../DependencyInjection/Fixtures/Resources/views/layout.html.hbs'))
        ;
        $loader = new FilesystemLoader($locator, $parser);

        $this->assertTrue($loader->exists($template));
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testErrorIfLocatorThrowsInvalid()
    {
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser
            ->expects($this->once())
            ->method('parse')
            ->with('name.format.engine')
            ->will($this->returnValue(new TemplateReference('', '', 'name', 'format', 'engine')))
        ;

        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->throwException(new \InvalidArgumentException('Unable to find template "NonExistent".')))
        ;

        $loader = new FilesystemLoader($locator, $parser);
        $loader->getCacheKey('name.format.engine');
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testErrorIfLocatorReturnsFalse()
    {
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser
            ->expects($this->once())
            ->method('parse')
            ->with('name.format.engine')
            ->will($this->returnValue(new TemplateReference('', '', 'name', 'format', 'engine')))
        ;

        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnValue(false))
        ;

        $loader = new FilesystemLoader($locator, $parser);
        $loader->getCacheKey('name.format.engine');
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     * @expectedExceptionMessageRegExp /Unable to find template "name\.format\.engine" \(looked into: .*Tests.Loader.\.\..DependencyInjection.Fixtures.Resources.views\)/
     */
    public function testErrorIfTemplateDoesNotExist()
    {
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator, $parser);
        $loader->addPath(__DIR__.'/../DependencyInjection/Fixtures/Resources/views');

        $method = new \ReflectionMethod('JaySDe\HandlebarsBundle\Loader\FilesystemLoader', 'findTemplate');
        $method->setAccessible(true);
        $method->invoke($loader, 'name.format.engine');
    }
}
