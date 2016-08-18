<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Helper;

use JaySDe\HandlebarsBundle\Helper\EsiHelper;

class EsiHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testFragmentRenderer()
    {
        $observer = $this->prophesize('\Symfony\Component\HttpKernel\Fragment\FragmentHandler');
        $observer->render('test', 'esi', [])->willReturn('test')->shouldBeCalled();

        $helper = new EsiHelper($observer->reveal());
        $result = $helper->handle('test', []);

        $this->assertInstanceOf('\LightnCandy\SafeString', $result);
        $this->assertSame('test', (string)$result);
    }
}
