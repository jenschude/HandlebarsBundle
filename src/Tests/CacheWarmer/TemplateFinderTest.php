<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\CacheWarmer;


use JaySDe\HandlebarsBundle\CacheWarmer\TemplateFinder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Templating\TemplateNameParser;

class TemplateFinderTest extends TestCase
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
        $this->assertSame(['layout.html.hbs', 'main.hbs', 'partial.handlebars'], $names);
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
