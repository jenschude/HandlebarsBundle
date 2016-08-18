<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\DependencyInjection\Compiler;


use JaySDe\HandlebarsBundle\DependencyInjection\Compiler\HelperPass;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Reference;

class HelperPassTest extends \PHPUnit_Framework_TestCase
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
