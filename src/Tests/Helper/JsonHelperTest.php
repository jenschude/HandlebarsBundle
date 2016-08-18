<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Helper;


use JaySDe\HandlebarsBundle\Helper\JsonHelper;

class JsonHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $data = ['foo' => 'bar'];
        $helper = new JsonHelper();

        $result = $helper->handle($data, []);
        $this->assertJsonStringEqualsJsonString('{"foo": "bar"}', $result);
    }
}
