<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests;


use JaySDe\HandlebarsBundle\HandlebarsProfileExtension;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class HandlebarsProfileExtensionTest extends TestCase
{
    public function testEnterLeave()
    {
        $mainProfile = $this->prophesize('Twig_Profiler_Profile');
        $mainProfile->addProfile(Argument::type('Twig_Profiler_Profile'))->shouldBeCalled();
        $mainProfile->leave()->shouldBeCalled();

        $watchEvent = $this->prophesize(StopwatchEvent::class);
        $watchEvent->stop()->shouldBeCalled();
        $watch = $this->prophesize(Stopwatch::class);
        $watch->start('test', 'template')->willReturn($watchEvent->reveal())->shouldBeCalled();

        $profiler = new HandlebarsProfileExtension($mainProfile->reveal(), $watch->reveal());

        $profile = $this->prophesize('Twig_Profiler_Profile');
        $profile->getName()->willReturn('test');
        $profile->isTemplate()->willReturn(true);
        $profile->leave()->shouldBeCalled();

        $profileInstance = $profile->reveal();
        $profiler->enter($profileInstance);
        $profiler->leave($profileInstance);
    }

    public function testGetName()
    {
        $mainProfile = $this->prophesize('Twig_Profiler_Profile');
        $watch = $this->prophesize('Symfony\Component\Stopwatch\Stopwatch');
        $profiler = new HandlebarsProfileExtension($mainProfile->reveal(), $watch->reveal());
        $this->assertSame('profiler', $profiler->getName());
    }
}
