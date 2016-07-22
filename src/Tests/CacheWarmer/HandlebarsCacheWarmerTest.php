<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\CacheWarmer;


use JaySDe\HandlebarsBundle\CacheWarmer\HandlebarsCacheWarmer;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class HandlebarsCacheWarmerTest extends \PHPUnit_Framework_TestCase
{
    public function testOptional()
    {
        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $finder = $this->prophesize('Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface');
        $warmer = new HandlebarsCacheWarmer($container->reveal(), $finder->reveal());
        $this->assertTrue($warmer->isOptional());
    }

    public function testWarmup()
    {
        $handlebars = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsEnvironment');
        $handlebars->compile(Argument::type('Symfony\Component\Templating\TemplateReferenceInterface'))->shouldBeCalled();

        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->get('handlebars')->willReturn($handlebars->reveal());
        $finder = $this->prophesize('Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface');

        $template1 = $this->prophesize('Symfony\Component\Templating\TemplateReferenceInterface');
        $template1->get('engine')->willReturn('hbs');

        $template2 = $this->prophesize('Symfony\Component\Templating\TemplateReferenceInterface');
        $template2->get('engine')->willReturn('twig');

        $finder->findAllTemplates()->willReturn([$template1->reveal(), $template2->reveal()]);
        $warmer = new HandlebarsCacheWarmer($container->reveal(), $finder->reveal());
        
        $warmer->warmUp('');
    }

    public function testCompileException()
    {
        $handlebars = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsEnvironment');
        $handlebars->compile(Argument::type('Symfony\Component\Templating\TemplateReferenceInterface'))
            ->willThrow(new \Exception("test"));

        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $logger->warning('Failed to compile Handlebars template "template1": "test"')->shouldBeCalled();
        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->get('handlebars')->willReturn($handlebars->reveal());

        $finder = $this->prophesize('Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface');

        $template1 = $this->prophesize('Symfony\Component\Templating\TemplateReferenceInterface');
        $template1->get('engine')->willReturn('hbs');
        $template1->__toString()->willReturn('template1');
        $finder->findAllTemplates()->willReturn([$template1->reveal()]);
        $warmer = new HandlebarsCacheWarmer($container->reveal(), $finder->reveal(), $logger->reveal());

        $warmer->warmUp('');
    }

}
