<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle;

use JaySDe\HandlebarsBundle\Helper\HelperInterface;

class HandlebarsHelper
{
    private $helpers = [];

    public function addHelper($id, $helper)
    {
        if ($helper instanceof HelperInterface) {
            $this->helpers[$id] = [$helper, 'handle'];
        } elseif (method_exists($helper, 'execute')) {
            // This is a temporary measure until we decide on a common interface.
            // I'm relaxing the argument type above, but doing the check here to
            // determine the helper's invocation method
            $this->helpers[$id] = [$helper, 'execute'];
        } elseif (is_callable($helper)) {
            $this->helpers[$id] = $helper;
        }
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
