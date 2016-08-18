<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Tests\Loader;

use JaySDe\HandlebarsBundle\Error\LoaderException;
use JaySDe\HandlebarsBundle\Loader\FilesystemLoader;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;

class FilesystemLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSource()
    {
        $parser = $this->getMockBuilder('Symfony\Component\Templating\TemplateNameParserInterface')->getMock();
        $locator = $this->getMockBuilder('Symfony\Component\Config\FileLocatorInterface')->getMock();
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnValue(__DIR__.'/../Fixtures/Resources/views/layout.html.hbs'))
        ;
        $loader = new FilesystemLoader($locator, $parser);
        $loader->addPath(__DIR__.'/../Fixtures/Resources/views', 'namespace');
        // Twig-style
        $this->assertEquals("This is a layout\n", $loader->getSource('@namespace/layout.html.hbs'));
        // Symfony-style
        $this->assertEquals("This is a layout\n", $loader->getSource('HandlebarsBundle::layout.html.hbs'));
    }

    public function testExists()
    {
        // should return true for templates that Handlebars does not find, but Symfony does
        $parser = $this->getMockBuilder('Symfony\Component\Templating\TemplateNameParserInterface')->getMock();
        $locator = $this->getMockBuilder('Symfony\Component\Config\FileLocatorInterface')->getMock();
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnValue($template = __DIR__.'/../Fixtures/Resources/views/layout.html.hbs'))
        ;
        $loader = new FilesystemLoader($locator, $parser);

        $this->assertTrue($loader->exists($template));
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testErrorIfLocatorThrowsInvalid()
    {
        $parser = $this->getMockBuilder('Symfony\Component\Templating\TemplateNameParserInterface')->getMock();
        $parser
            ->expects($this->once())
            ->method('parse')
            ->with('name.format.engine')
            ->will($this->returnValue(new TemplateReference('', '', 'name', 'format', 'engine')))
        ;

        $locator = $this->getMockBuilder('Symfony\Component\Config\FileLocatorInterface')->getMock();
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
        $parser = $this->getMockBuilder('Symfony\Component\Templating\TemplateNameParserInterface')->getMock();
        $parser
            ->expects($this->once())
            ->method('parse')
            ->with('name.format.engine')
            ->will($this->returnValue(new TemplateReference('', '', 'name', 'format', 'engine')))
        ;

        $locator = $this->getMockBuilder('Symfony\Component\Config\FileLocatorInterface')->getMock();
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
     * @expectedExceptionMessageRegExp /Unable to find template "name\.format\.engine" \(looked into: .*Tests.Loader.\.\..Fixtures.Resources.views\)/
     */
    public function testErrorIfTemplateDoesNotExist()
    {
        $parser = $this->getMockBuilder('Symfony\Component\Templating\TemplateNameParserInterface')->getMock();
        $locator = $this->getMockBuilder('Symfony\Component\Config\FileLocatorInterface')->getMock();

        $loader = new FilesystemLoader($locator, $parser);
        $loader->addPath(__DIR__.'/../Fixtures/Resources/views');

        $method = new \ReflectionMethod('JaySDe\HandlebarsBundle\Loader\FilesystemLoader', 'findTemplate');
        $method->setAccessible(true);
        $method->invoke($loader, 'name.format.engine');
    }

    public function testGetPaths()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->setPaths([__DIR__]);

        $this->assertSame([__DIR__], $loader->getPaths());
    }

    public function testSetPath()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->setPaths(__DIR__);

        $this->assertSame([__DIR__], $loader->getPaths());
    }

    public function testGetNamespacePaths()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->setPaths([__DIR__], 'test');

        $this->assertSame([__DIR__], $loader->getPaths('test'));
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testSetUnknownPath()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->setPaths(['test']);
    }

    public function testAddPath()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__);

        $this->assertSame([__DIR__], $loader->getPaths());
    }

    public function testAddNamespacePath()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__, 'test');

        $this->assertSame([__DIR__], $loader->getPaths('test'));
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testAddUnknownPath()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath('test');
    }

    public function testPrependPath()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__.'/../Fixtures');
        $loader->prependPath(__DIR__);

        $this->assertSame([__DIR__, __DIR__.'/../Fixtures'], $loader->getPaths());
    }

    public function testPrependNamespacePath()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->prependPath(__DIR__, 'test');

        $this->assertSame([__DIR__], $loader->getPaths('test'));
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testPrependUnknownPath()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->prependPath('test');
    }

    public function testNamespaces()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__);
        $loader->addPath(__DIR__, 'test');

        $this->assertSame([$loader::MAIN_NAMESPACE, 'test'], $loader->getNamespaces());
    }

    public function testKnownTemplateExists()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser->parse('main')->willReturn('main');
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');
        $locator->locate('main')->willReturn('main.hbs');

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__.'/../Fixtures/Resources/views');

        $this->assertTrue($loader->exists('main'));
        $this->assertTrue($loader->exists('main'));
    }

    public function testKnownTemplateCache()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser->parse('main')->willReturn('main')->shouldBeCalledTimes(1);
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');
        $locator->locate('main')->willReturn(__DIR__ . '/../Fixtures/Resources/views/main.hbs')->shouldBeCalledTimes(1);

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__.'/../Fixtures/Resources/views');

        $this->assertTrue($loader->exists('main'));
        $this->assertSame('Hello {{>partial }}', trim($loader->getSource('main')));
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testUnknownTemplateCache()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser->parse('main')->willReturn('main')->shouldBeCalledTimes(1);
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');
        $locator->locate('main')->willReturn(false)->shouldBeCalledTimes(1);

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__.'/../Fixtures/Resources/views');

        $this->assertFalse($loader->exists('main'));
        $this->assertFalse($loader->exists('main'));
        $loader->getSource('main');
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testUnknownTemplateNamespace()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser->parse('main')->shouldNotBeCalled();
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');
        $locator->locate('main')->shouldNotBeCalled();

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__.'/../Fixtures/Resources/views');

        $loader->getSource('@test/main');
    }

    /**
     * @expectedException \JaySDe\HandlebarsBundle\Error\LoaderException
     */
    public function testMalformedTemplateNamespace()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser->parse('main')->shouldNotBeCalled();
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');
        $locator->locate('main')->shouldNotBeCalled();

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__.'/../Fixtures/Resources/views');

        $loader->getSource('@test:main');
    }

    public function testUnknownTemplateNamespaceExists()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser->parse('main')->shouldNotBeCalled();
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');
        $locator->locate('main')->shouldNotBeCalled();

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__.'/../Fixtures/Resources/views');

        $this->assertFalse($loader->exists('@test/main'));
    }

    public function testExistsException()
    {
        $parser = $this->prophesize('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser->parse('main')->shouldNotBeCalled();
        $locator = $this->prophesize('Symfony\Component\Config\FileLocatorInterface');
        $locator->locate('main')->shouldNotBeCalled();

        $loader = new FilesystemLoader($locator->reveal(), $parser->reveal());
        $loader->addPath(__DIR__.'/../Fixtures/Resources/views');

        $this->assertFalse($loader->exists('@test:main'));
    }
}
