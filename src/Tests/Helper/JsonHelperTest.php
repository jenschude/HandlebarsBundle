<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Helper;


use JaySDe\HandlebarsBundle\Helper\JsonHelper;
use PHPUnit\Framework\TestCase;

class JsonHelperTest extends TestCase
{
    public function testHandle()
    {
        $data = ['foo' => 'bar'];
        $helper = new JsonHelper();

        $result = $helper->handle($data, []);
        $this->assertJsonStringEqualsJsonString('{"foo": "bar"}', $result);
    }
}
