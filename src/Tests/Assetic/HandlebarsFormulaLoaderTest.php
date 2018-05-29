<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Assetic;


use JaySDe\HandlebarsBundle\Assetic\HandlebarsFormulaLoader;
use PHPUnit\Framework\TestCase;

class HandlebarsFormulaLoaderTest extends TestCase
{
    public function testLoad()
    {
        $environment = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsEnvironment');
        $resource = $this->prophesize('Assetic\Factory\Resource\ResourceInterface');
        $loader = new HandlebarsFormulaLoader($environment->reveal());
        $this->assertEmpty($loader->load($resource->reveal()));
    }
}
