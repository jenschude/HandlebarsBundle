<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests;


use JaySDe\HandlebarsBundle\HandlebarsHelperService;
use JaySDe\HandlebarsBundle\Tests\Fixtures\InvokeHelper;

class HandlebarsHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testAddHelper()
    {
        $helperService = new HandlebarsHelperService();
        $observer = $this->prophesize('\JaySDe\HandlebarsBundle\Helper\HelperInterface');
        $helperService->addHelper('test', $observer);
    }

    public function testGetHelperMethods()
    {
        $helperService = new HandlebarsHelperService();
        $observer = $this->prophesize('\JaySDe\HandlebarsBundle\Helper\HelperInterface');
        $helperService->addHelper('test', $observer->reveal());

        $this->assertSame(['test'], $helperService->getHelperMethods());
    }

    public function testGetHelpers()
    {
        $helperService = new HandlebarsHelperService();
        $observer = $this->prophesize('\JaySDe\HandlebarsBundle\Helper\HelperInterface');
        $helper = $observer->reveal();
        $helperService->addHelper('test', $helper);

        $helpers = $helperService->getHelpers();
        $this->assertArrayHasKey('test', $helpers);
        $this->assertTrue(is_callable($helpers['test']));
        $this->assertSame([$helper, 'handle'], $helpers['test']);
    }

    public function testCallable()
    {
        $helperService = new HandlebarsHelperService();
        $t = function () {};
        $helperService->addHelper('test', $t);

        $helper = $helperService->getHelpers()['test'];
        $this->assertTrue(is_callable($helper));
        $this->assertSame($t, $helper);
    }

    public function testInvoke()
    {
        $helperService = new HandlebarsHelperService();
        $t = new InvokeHelper();
        $helperService->addHelper('test', $t);

        $this->assertInstanceOf(
            '\JaySDe\HandlebarsBundle\Tests\Fixtures\InvokeHelper',
            $helperService->getHelpers()['test']
        );
        $this->assertSame($t, $helperService->getHelpers()['test']);
    }
}
