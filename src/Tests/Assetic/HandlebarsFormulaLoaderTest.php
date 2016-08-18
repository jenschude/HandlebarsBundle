<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Assetic;


use JaySDe\HandlebarsBundle\Assetic\HandlebarsFormulaLoader;

class HandlebarsFormulaLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $environment = $this->prophesize('JaySDe\HandlebarsBundle\HandlebarsEnvironment');
        $resource = $this->prophesize('Assetic\Factory\Resource\ResourceInterface');
        $loader = new HandlebarsFormulaLoader($environment->reveal());
        $this->assertEmpty($loader->load($resource->reveal()));
    }
}
