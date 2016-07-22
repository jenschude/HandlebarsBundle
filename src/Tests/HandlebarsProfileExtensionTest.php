<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests;


use JaySDe\HandlebarsBundle\HandlebarsProfileExtension;
use Prophecy\Argument;

class HandlebarsProfileExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testEnterLeave()
    {
        $mainProfile = $this->prophesize('Twig_Profiler_Profile');
        $mainProfile->addProfile(Argument::type('Twig_Profiler_Profile'))->shouldBeCalled();
        $mainProfile->leave()->shouldBeCalled();

        $watchEvent = $this->prophesize('Symfony\Component\Stopwatch\StopwatchEvent');
        $watchEvent->stop()->shouldBeCalled();
        $watch = $this->prophesize('Symfony\Component\Stopwatch\Stopwatch');
        $watch->start('test', 'template')->willReturn($watchEvent->reveal())->shouldBeCalled();
        $watch->stop()->shouldBeCalled();

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
