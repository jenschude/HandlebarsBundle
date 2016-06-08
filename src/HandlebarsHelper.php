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
        $this->helpers[$id] = '\\' . get_class($helper) . '::handle';
    }

    public function getHelpers()
    {
        return $this->helpers;
    }
}
