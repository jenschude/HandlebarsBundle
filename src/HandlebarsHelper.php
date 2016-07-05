<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle;

use JaySDe\HandlebarsBundle\Helper\HelperInterface;

class HandlebarsHelper
{
    private $helpers = [];

    public function addHelper($id, HelperInterface $helper)
    {
        $this->helpers[$id] = [$helper, 'handle'];
    }

    public function getHelperMethods()
    {
        return array_keys($this->helpers);
    }

    public function getHelpers()
    {
        return $this->helpers;
    }
}
