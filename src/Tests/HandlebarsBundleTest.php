<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests;


use JaySDe\HandlebarsBundle\DependencyInjection\Compiler\HelperPass;
use JaySDe\HandlebarsBundle\HandlebarsBundle;
use PHPUnit\Framework\TestCase;

class HandlebarsBundleTest extends TestCase
{
    public function testBuild()
    {
        $observer = $this->prophesize('\Symfony\Component\DependencyInjection\ContainerBuilder');
        $observer->addCompilerPass(new HelperPass())->shouldBecalled();
        $bundle = new HandlebarsBundle();
        $bundle->build($observer->reveal());
    }
}
