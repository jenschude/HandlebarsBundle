<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Tests\Fixtures;

class InvokeHelper
{
    public function __invoke($context)
    {
        return $context;
    }
}
