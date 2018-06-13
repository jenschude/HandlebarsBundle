<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\DependencyInjection\Compiler;


use JaySDe\HandlebarsBundle\DependencyInjection\Compiler\HelperPass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Reference;

class HelperPassTest extends TestCase
{
    public function testTagging()
    {
        $definitionObserver = $this->prophesize('\Symfony\Component\DependencyInjection\Definition');
        $definitionObserver->addMethodCall(
            "addHelper",
            Argument::allOf(
                Argument::containing('test'),
                Argument::containing(new Reference('handlebars.helper.test'))
            )
        )->shouldBeCalled();
        $containerObserver = $this->prophesize('\Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerObserver->has('handlebars.helper')->willReturn(true)->shouldBeCalled();
        $containerObserver->findDefinition('handlebars.helper')->willReturn($definitionObserver->reveal())->shouldBeCalled();

        $taggedServices = [
            'handlebars.helper.test' => [
                ['id' => 'test']
            ]
        ];
        $containerObserver->findTaggedServiceIds('handlebars.helper')->willReturn($taggedServices)->shouldBeCalled();

        $helperPass = new HelperPass();
        $helperPass->process($containerObserver->reveal());
    }

    public function testDisabled()
    {
        $containerObserver = $this->prophesize('\Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerObserver->has('handlebars.helper')->willReturn(false)->shouldBeCalled();

        $helperPass = new HelperPass();
        $helperPass->process($containerObserver->reveal());
    }
}
