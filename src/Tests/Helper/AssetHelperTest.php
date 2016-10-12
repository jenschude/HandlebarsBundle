<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Helper;


use JaySDe\HandlebarsBundle\Helper\AssetHelper;

class AssetHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testAsset()
    {
        $packages = $this->prophesize('Symfony\Component\Asset\Packages');
        $packages->getUrl('_welcome', null)->willReturn('http://example.com/asset.png')->shouldBeCalled();
        $helper = new AssetHelper($packages->reveal(), 'url');

        $data = '_welcome';
        $result = $helper->handle($data, []);
        $this->assertSame('http://example.com/asset.png', $result);
    }

    public function testPackageAsset()
    {
        $packages = $this->prophesize('Symfony\Component\Asset\Packages');
        $packages->getUrl('_welcome', 'default')->willReturn('http://example.com/asset.png')->shouldBeCalled();
        $helper = new AssetHelper($packages->reveal(), 'url');

        $data = '_welcome';
        $result = $helper->handle($data, ['hash' => ['package' => 'default']]);
        $this->assertSame('http://example.com/asset.png', $result);
    }
}
