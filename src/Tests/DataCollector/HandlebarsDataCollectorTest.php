<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\DataCollector;


use JaySDe\HandlebarsBundle\DataCollector\HandlebarsDataCollector;
use PHPUnit\Framework\TestCase;

class HandlebarsDataCollectorTest extends TestCase
{
    public function testTemplateCount()
    {
        $profile = new \Twig_Profiler_Profile();

        $template1 = new \Twig_Profiler_Profile('template1', \Twig_Profiler_Profile::TEMPLATE, 'template1');
        $profile->addProfile($template1);
        $collector = new HandlebarsDataCollector($profile);

        $this->assertSame(1, $collector->getTemplateCount());
    }

    public function testRecursiveTemplateCount()
    {
        $profile = new \Twig_Profiler_Profile();

        $template1 = new \Twig_Profiler_Profile('template1', \Twig_Profiler_Profile::TEMPLATE, 'template1');
        $template2 = new \Twig_Profiler_Profile('template2', \Twig_Profiler_Profile::TEMPLATE, 'template2');
        $template3 = new \Twig_Profiler_Profile('template3', \Twig_Profiler_Profile::TEMPLATE, 'template3');
        $template1->addProfile($template2);
        $template1->addProfile($template3);

        $template4 = new \Twig_Profiler_Profile('template2', \Twig_Profiler_Profile::TEMPLATE, 'template2');
        $profile->addProfile($template1);
        $profile->addProfile($template4);
        $collector = new HandlebarsDataCollector($profile);

        $this->assertSame(4, $collector->getTemplateCount());
    }

    public function testBlockCount()
    {
        $profile = new \Twig_Profiler_Profile();

        $template1 = new \Twig_Profiler_Profile('block1', \Twig_Profiler_Profile::BLOCK, 'block1');
        $profile->addProfile($template1);
        $collector = new HandlebarsDataCollector($profile);

        $this->assertSame(1, $collector->getBlockCount());
    }

    public function testRecursiveBlockCount()
    {
        $profile = new \Twig_Profiler_Profile();

        $template1 = new \Twig_Profiler_Profile('block1', \Twig_Profiler_Profile::BLOCK, 'block1');
        $template2 = new \Twig_Profiler_Profile('block2', \Twig_Profiler_Profile::BLOCK, 'block2');
        $template3 = new \Twig_Profiler_Profile('block3', \Twig_Profiler_Profile::BLOCK, 'block3');
        $template1->addProfile($template2);
        $template1->addProfile($template3);

        $template4 = new \Twig_Profiler_Profile('block4', \Twig_Profiler_Profile::BLOCK, 'block4');
        $profile->addProfile($template1);
        $profile->addProfile($template4);
        $collector = new HandlebarsDataCollector($profile);

        $this->assertSame(4, $collector->getBlockCount());
    }

    public function testMacroCount()
    {
        $profile = new \Twig_Profiler_Profile();

        $template1 = new \Twig_Profiler_Profile('block1', \Twig_Profiler_Profile::MACRO, 'block1');
        $profile->addProfile($template1);
        $collector = new HandlebarsDataCollector($profile);

        $this->assertSame(1, $collector->getMacroCount());
    }

    public function testRecursiveMacroCount()
    {
        $profile = new \Twig_Profiler_Profile();

        $template1 = new \Twig_Profiler_Profile('block1', \Twig_Profiler_Profile::MACRO, 'block1');
        $template2 = new \Twig_Profiler_Profile('block2', \Twig_Profiler_Profile::MACRO, 'block2');
        $template3 = new \Twig_Profiler_Profile('block3', \Twig_Profiler_Profile::MACRO, 'block3');
        $template1->addProfile($template2);
        $template1->addProfile($template3);

        $template4 = new \Twig_Profiler_Profile('block4', \Twig_Profiler_Profile::MACRO, 'block4');
        $profile->addProfile($template1);
        $profile->addProfile($template4);
        $collector = new HandlebarsDataCollector($profile);

        $this->assertSame(4, $collector->getMacroCount());
    }

    public function testGetTemplates()
    {
        $profile = new \Twig_Profiler_Profile();

        $template1 = new \Twig_Profiler_Profile('template1', \Twig_Profiler_Profile::TEMPLATE, 'template1');
        $template2 = new \Twig_Profiler_Profile('template2', \Twig_Profiler_Profile::TEMPLATE, 'template2');
        $template3 = new \Twig_Profiler_Profile('template3', \Twig_Profiler_Profile::TEMPLATE, 'template3');
        $template1->addProfile($template2);
        $template1->addProfile($template3);

        $template4 = new \Twig_Profiler_Profile('template2', \Twig_Profiler_Profile::TEMPLATE, 'template2');
        $profile->addProfile($template1);
        $profile->addProfile($template4);
        $collector = new HandlebarsDataCollector($profile);

        $this->assertSame(['template1' => 1, 'template2' => 2, 'template3' => 1], $collector->getTemplates());
    }

    public function testGetTime()
    {
        $profile = new \Twig_Profiler_Profile();

        $template1 = new \Twig_Profiler_Profile('template1', \Twig_Profiler_Profile::TEMPLATE, 'template1');
        $profile->addProfile($template1);
        $profile->enter();
        $template1->enter();
        usleep(10000);
        $template1->leave();
        $profile->leave();
        $collector = new HandlebarsDataCollector($profile);
        $this->assertGreaterThanOrEqual(10, $collector->getTime());
    }

    public function testGetName()
    {
        $profile = new \Twig_Profiler_Profile();
        $collector = new HandlebarsDataCollector($profile);
        $this->assertSame('handlebars', $collector->getName());
    }

    public function testHtmlGraph()
    {
        $profile = new \Twig_Profiler_Profile();

        $template1 = new \Twig_Profiler_Profile('template1', \Twig_Profiler_Profile::TEMPLATE, 'template1');
        $profile->addProfile($template1);
        $profile->enter();
        $template1->enter();
        usleep(10000);
        $template1->leave();
        $profile->leave();
        $collector = new HandlebarsDataCollector($profile);

        $this->assertContains('template1', (string)$collector->getHtmlCallGraph());
    }
}
