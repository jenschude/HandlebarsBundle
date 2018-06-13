<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Helper;


use JaySDe\HandlebarsBundle\Helper\TranslationHelper;
use PHPUnit\Framework\TestCase;

class TranslationHelperTest extends TestCase
{
    public function testTranslate()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->trans('Hello world!', [], null, null)->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), null, '%', '%');
        $trans = $helper->handle('Hello world!', []);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }

    public function testNamespaceContext()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->trans('Hello world!', ['%bundle%' => 'main'], 'main', null)->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), null, '%', '%');
        $trans = $helper->handle('main:Hello world!', []);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }

    public function testDefaultNamespace()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->trans('Hello world!', [], 'main', null)->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), 'main', '%', '%');
        $trans = $helper->handle('Hello world!', []);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }

    public function testInterpolationPrefix()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->trans('Hello world!', ['__bundle%' => 'main'], 'main', null)
            ->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), null, '__', '%');
        $trans = $helper->handle('main:Hello world!', []);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }

    public function testInterpolationSuffix()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->trans('Hello world!', ['%bundle__' => 'main'], 'main', null)
            ->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), null, '%', '__');
        $trans = $helper->handle('main:Hello world!', []);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }

    public function testInterpolation()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->trans('Hello world!', ['__bundle__' => 'main'], 'main', null)
            ->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), null, '__', '__');
        $trans = $helper->handle('main:Hello world!', []);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }

    public function testLocale()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->trans('Hello world!', ['%bundle%' => 'main', '%locale%' => 'en'], 'main', 'en')
            ->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), null, '%', '%');
        $trans = $helper->handle('main:Hello world!', ['hash' => ['locale' => 'en']]);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }

    public function testCount()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->transChoice('Hello world!', 3, ['%bundle%' => 'main', '%count%' => 3], 'main', null)
            ->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), null, '%', '%');
        $trans = $helper->handle('main:Hello world!', ['hash' => ['count' => 3]]);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }

    public function testCountLocale()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->transChoice(
            'Hello world!', 3, ['%bundle%' => 'main', '%count%' => 3, '%locale%' => 'en'], 'main', 'en'
        )->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), null, '%', '%');
        $trans = $helper->handle('main:Hello world!', ['hash' => ['count' => 3, 'locale' => 'en']]);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }

    public function testBundle()
    {
        $translator = $this->prophesize('Symfony\Component\Translation\TranslatorInterface');
        $translator->transChoice(
            'Hello world!', 3, ['%bundle%' => 'main', '%count%' => 3, '%locale%' => 'en'], 'main', 'en'
        )->willReturn('Hallo Welt!')->shouldBeCalled();

        $helper = new TranslationHelper($translator->reveal(), null, '%', '%');
        $trans = $helper->handle('Hello world!', ['hash' => ['bundle' => 'main', 'count' => 3, 'locale' => 'en']]);
        $this->assertSame('Hallo Welt!', (string)$trans);
    }
}
