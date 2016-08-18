<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests;


use JaySDe\HandlebarsBundle\HandlebarsEngine;

class HandlebarsEngineTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $environment = $this->prophesize('\JaySDe\HandlebarsBundle\HandlebarsEnvironment');
        $environment->render('test', [])->willReturn('test')->shouldBeCalled();
        $parser = $this->prophesize('\Symfony\Component\Templating\TemplateNameParserInterface');
        $engine = new HandlebarsEngine($environment->reveal(), $parser->reveal());

        $result = $engine->render('test', []);
        $this->assertSame('test', $result);
    }

    public function testExists()
    {
        $loader = $this->prophesize('\JaySDe\HandlebarsBundle\Loader\FilesystemLoader');
        $loader->exists('test')->willReturn(true)->shouldBeCalled();

        $environment = $this->prophesize('\JaySDe\HandlebarsBundle\HandlebarsEnvironment');
        $environment->getLoader()->willReturn($loader->reveal())->shouldBeCalled();

        $parser = $this->prophesize('\Symfony\Component\Templating\TemplateNameParserInterface');
        $engine = new HandlebarsEngine($environment->reveal(), $parser->reveal());

        $result = $engine->exists('test');
        $this->assertTrue($result);
    }

    public function getEngineType()
    {
        return [
            ['hbs', true],
            ['handlebars', true],
            ['twig', false],
            ['foo', false],
        ];
    }
    /**
     * @dataProvider getEngineType
     * @param $engineType
     */
    public function testSupports($engineType, $expectedResult)
    {
        $template = $this->prophesize('\Symfony\Component\Templating\TemplateReferenceInterface');
        $template->get('engine')->willReturn($engineType)->shouldBeCalled();

        $environment = $this->prophesize('\JaySDe\HandlebarsBundle\HandlebarsEnvironment');

        $parser = $this->prophesize('\Symfony\Component\Templating\TemplateNameParserInterface');
        $parser->parse('test')->willReturn($template->reveal())->shouldBeCalled();

        $engine = new HandlebarsEngine($environment->reveal(), $parser->reveal());

        $result = $engine->supports('test');
        $this->assertSame($expectedResult, $result);
    }

    public function testRenderResponse()
    {
        $environment = $this->prophesize('\JaySDe\HandlebarsBundle\HandlebarsEnvironment');
        $environment->render('test.hbs', [])->willReturn('test')->shouldBeCalled();

        $parser = $this->prophesize('\Symfony\Component\Templating\TemplateNameParserInterface');

        $engine = new HandlebarsEngine($environment->reveal(), $parser->reveal());

        $result = $engine->renderResponse('test.hbs');

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $result);
        $this->assertSame('test', $result->getContent());
    }

    public function testRenderGivenResponse()
    {
        $environment = $this->prophesize('\JaySDe\HandlebarsBundle\HandlebarsEnvironment');
        $environment->render('test.hbs', ['foo' => 'bar'])->willReturn('test')->shouldBeCalled();

        $parser = $this->prophesize('\Symfony\Component\Templating\TemplateNameParserInterface');

        $engine = new HandlebarsEngine($environment->reveal(), $parser->reveal());

        $response = $this->prophesize('\Symfony\Component\HttpFoundation\Response');
        $response->setContent('test')->shouldBeCalled();

        $engine->renderResponse('test.hbs', ['foo' => 'bar'], $response->reveal());
    }
}
