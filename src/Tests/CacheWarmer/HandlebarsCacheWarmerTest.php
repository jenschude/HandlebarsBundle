<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\CacheWarmer;


use JaySDe\HandlebarsBundle\CacheWarmer\HandlebarsCacheWarmer;
use JaySDe\HandlebarsBundle\HandlebarsEngine;
use JaySDe\HandlebarsBundle\HandlebarsEnvironment;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class HandlebarsCacheWarmerTest extends TestCase
{
    public function testOptional()
    {
        $engine = $this->prophesize(HandlebarsEnvironment::class);
        $finder = $this->prophesize(TemplateFinderInterface::class);
        $warmer = new HandlebarsCacheWarmer($engine->reveal(), $finder->reveal());
        $this->assertTrue($warmer->isOptional());
    }

    public function testWarmup()
    {
        $handlebars = $this->prophesize(HandlebarsEnvironment::class);
        $handlebars->compile(Argument::type(TemplateReferenceInterface::class))->shouldBeCalled();

        $finder = $this->prophesize(TemplateFinderInterface::class);

        $template1 = $this->prophesize(TemplateReferenceInterface::class);
        $template1->get('engine')->willReturn('hbs');

        $template2 = $this->prophesize(TemplateReferenceInterface::class);
        $template2->get('engine')->willReturn('twig');

        $finder->findAllTemplates()->willReturn([$template1->reveal(), $template2->reveal()]);
        $warmer = new HandlebarsCacheWarmer($handlebars->reveal(), $finder->reveal());

        $warmer->warmUp('');
    }

    public function testCompileException()
    {
        $handlebars = $this->prophesize(HandlebarsEnvironment::class);
        $handlebars->compile(Argument::type(TemplateReferenceInterface::class))
            ->willThrow(new \Exception("test"));

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->warning('Failed to compile Handlebars template "template1": "test"')->shouldBeCalled();

        $finder = $this->prophesize(TemplateFinderInterface::class);

        $template1 = $this->prophesize(TemplateReferenceInterface::class);
        $template1->get('engine')->willReturn('hbs');
        $template1->__toString()->willReturn('template1');
        $finder->findAllTemplates()->willReturn([$template1->reveal()]);
        $warmer = new HandlebarsCacheWarmer($handlebars->reveal(), $finder->reveal(), $logger->reveal());

        $warmer->warmUp('');
    }

}
