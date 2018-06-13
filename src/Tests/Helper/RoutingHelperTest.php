<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Helper;


use JaySDe\HandlebarsBundle\Helper\RoutingHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RoutingHelperTest extends TestCase
{
    public function testUrl()
    {
        $generator = $this->prophesize('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->generate('_welcome', [], UrlGeneratorInterface::ABSOLUTE_URL)->willReturn('http://example.com/welcome')->shouldBeCalled();
        $helper = new RoutingHelper($generator->reveal(), 'url');

        $data = '_welcome';
        $result = $helper->handle($data, []);
        $this->assertSame('http://example.com/welcome', $result);
    }

    public function testPath()
    {
        $generator = $this->prophesize('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->generate('_welcome', [], UrlGeneratorInterface::ABSOLUTE_PATH)->willReturn('/welcome')->shouldBeCalled();
        $helper = new RoutingHelper($generator->reveal(), 'path');

        $data = '_welcome';
        $result = $helper->handle($data, []);
        $this->assertSame('/welcome', $result);
    }

    public function testUrlRelative()
    {
        $generator = $this->prophesize('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->generate('_welcome', ['relative' => true], UrlGeneratorInterface::NETWORK_PATH)->willReturn('http://example.com/welcome')->shouldBeCalled();
        $helper = new RoutingHelper($generator->reveal(), 'url');

        $data = '_welcome';
        $result = $helper->handle($data, ['hash' => ['relative' => true]]);
        $this->assertSame('http://example.com/welcome', $result);
    }

    public function testPathRelative()
    {
        $generator = $this->prophesize('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->generate('_welcome', ['relative' => true], UrlGeneratorInterface::RELATIVE_PATH)->willReturn('/welcome')->shouldBeCalled();
        $helper = new RoutingHelper($generator->reveal(), 'path');

        $data = '_welcome';
        $result = $helper->handle($data, ['hash' => ['relative' => true]]);
        $this->assertSame('/welcome', $result);
    }
}
