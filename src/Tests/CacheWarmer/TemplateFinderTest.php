<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\CacheWarmer;


use JaySDe\HandlebarsBundle\CacheWarmer\TemplateFinder;
use Symfony\Component\Templating\TemplateNameParser;

class TemplateFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testFindAllTemplates()
    {
        $parser = new TemplateNameParser();
        $finder = new TemplateFinder($parser, [__DIR__ . '/../Fixtures/Resources/views']);

        $templates = $finder->findAllTemplates();
        $this->assertCount(3, $templates);

        $names = [];
        foreach ($templates as $template) {
            $this->assertInstanceOf('Symfony\Component\Templating\TemplateReference', $template);
            $names[] = $template->getLogicalName();
        }
        sort($names);
        $this->assertSame(['layout.html.hbs', 'main.hbs', 'partial.hbs'], $names);
    }

    public function testRunOnce()
    {
        $parser = new TemplateNameParser();
        $finder = new TemplateFinder($parser, [__DIR__ . '/../Fixtures/Resources/views']);

        $templates = $finder->findAllTemplates();
        $this->assertCount(3, $templates);
        $this->assertSame($templates, $finder->findAllTemplates());
    }
}
