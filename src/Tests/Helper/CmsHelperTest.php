<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Helper;


use JaySDe\HandlebarsBundle\Helper\CmsHelper;
use PHPUnit\Framework\TestCase;

class CmsHelperTest extends TestCase
{
    public function testHandle()
    {
        $observer = $this->prophesize('\JaySDe\HandlebarsBundle\Helper\TranslationHelper');
        $observer->handle('test', [])->willReturn('lorem ipsum')->shouldBeCalled();
        $helper = new CmsHelper($observer->reveal());

        $test = $helper->handle('test', []);
        $this->assertSame('lorem ipsum', $test);
    }

    public function testHandleWithOptions()
    {
        $observer = $this->prophesize('\JaySDe\HandlebarsBundle\Helper\TranslationHelper');
        $observer->handle('test', ['foo' => 'bar'])->willReturn('lorem ipsum')->shouldBeCalled();
        $helper = new CmsHelper($observer->reveal());

        $test = $helper->handle('test', ['hash' => ['foo' => 'bar']]);
        $this->assertSame('lorem ipsum', $test);
    }

    public function testHandleWithBundle()
    {
        $observer = $this->prophesize('\JaySDe\HandlebarsBundle\Helper\TranslationHelper');
        $observer->handle('foo:test', [])->willReturn('lorem ipsum')->shouldBeCalled();
        $helper = new CmsHelper($observer->reveal());

        $test = $helper->handle('test', ['hash' => ['bundle' => 'foo']]);
        $this->assertSame('lorem ipsum', $test);
    }

    public function testHandleWithOptionsAndBundle()
    {
        $observer = $this->prophesize('\JaySDe\HandlebarsBundle\Helper\TranslationHelper');
        $observer->handle('foo:test', ['foo' => 'bar'])->willReturn('lorem ipsum')->shouldBeCalled();
        $helper = new CmsHelper($observer->reveal());

        $test = $helper->handle('test', ['hash' => ['foo' => 'bar', 'bundle' => 'foo']]);
        $this->assertSame('lorem ipsum', $test);
    }

    public function testDefaultNamespace()
    {
        $observer = $this->prophesize('\JaySDe\HandlebarsBundle\Helper\TranslationHelper');
        $observer->handle('cms:test', [])->willReturn('lorem ipsum')->shouldBeCalled();
        $helper = new CmsHelper($observer->reveal(), 'cms');

        $test = $helper->handle('test', []);
        $this->assertSame('lorem ipsum', $test);
    }
}
