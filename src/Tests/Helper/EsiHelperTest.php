<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Helper;

use JaySDe\HandlebarsBundle\Helper\EsiHelper;
use PHPUnit\Framework\TestCase;

class EsiHelperTest extends TestCase
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
