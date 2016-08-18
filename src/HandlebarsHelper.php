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

    public function addHelper($id, $helper)
    {
        // This is a temporary measure until we decide on a common interface.
        // I'm relaxing the argument type above, but doing the check here to
        // determine the helper's invocation method

        if ($helper instanceof HelperInterface) {
            $method = 'handle';
        } else {
            $method = 'execute';
        }

        $this->helperMethods[$id] = get_class($helper) . '::' . $method;
        $this->helpers[$id] = [$helper, $method];
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
