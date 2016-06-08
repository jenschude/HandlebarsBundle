<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle;

use JaySDe\HandlebarsBundle\Helper\HelperInterface;

class HandlebarsHelper
{
    private $helperMethods = [];
    private $helpers = [];

    public function addHelper($id, HelperInterface $helper)
    {
        $this->helperMethods[$id] = get_class($helper) . '::handle';
        $this->helpers[$id] = [$helper, 'handle'];
    }

    public function getHelperMethods()
    {
        return $this->helperMethods;
    }

    public function getHelpers()
    {
        return $this->helpers;
    }
}
