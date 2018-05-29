<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\DependencyInjection;


use JaySDe\HandlebarsBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), []);

        $this->assertEquals(
            $this->getBundleDefaultConfig(),
            $config
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidFlag()
    {
        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [['flags' => ['FLAG_INVALID']]]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidExceptFlag()
    {
        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [['excludeFlags' => ['FLAG_INVALID']]]);
    }

    public function testFlag()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [['flags' => ['FLAG_BESTPERFORMANCE']]]);
        $this->assertContains('FLAG_BESTPERFORMANCE', $config['flags']);
    }

    public function testExceptFlag()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [['excludeFlags' => ['FLAG_BESTPERFORMANCE']]]);
        $this->assertContains('FLAG_BESTPERFORMANCE', $config['excludeFlags']);
    }

    public function testPaths()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(
            new Configuration(),
            [[
                'paths' => [
                    '%kernel.root_dir%/Resources/views',
                    '%kernel.root_dir%/../vendor/commercetools/sunrise-theme/templates'
                ]
            ]]
        );
        $this->assertArrayHasKey('%kernel.root_dir%/Resources/views', $config['paths']);
        $this->assertArrayHasKey('%kernel.root_dir%/../vendor/commercetools/sunrise-theme/templates', $config['paths']);
    }

    protected function getBundleDefaultConfig()
    {
        return array(
            'cache' => '%kernel.cache_dir%/handlebars',
            'debug' => '%kernel.debug%',
            'auto_reload' => '%kernel.debug%',
            'cms' => [
                'default_namespace' => null
            ],
            'translation' => [
                'default_namespace' => null,
                'interpolation_prefix' => '%',
                'interpolation_suffix' => '%',
            ],
            'flags' => [],
            'excludeFlags' => [],
            'paths' => []
        );
    }
}
