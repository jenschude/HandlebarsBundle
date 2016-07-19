<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle;

class HandlebarsHelper
{
    private $interface;
    private $handleMethod;
    private $helpers = [];

    public function __construct($interface, $handleMethod)
    {
        $this->interface = $interface;
        $this->handleMethod = $handleMethod;
    }

    public function addHelper($id, $helper)
    {
        if (!$helper instanceof $this->interface) {
            throw new \InvalidArgumentException(
                sprintf('Helper class "%s" is no instance of "%s"', get_class($helper), $this->interface)
            );
        }
        $callback = [$helper, $this->handleMethod];
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(
                sprintf('Method "%s" doesn\'t exist in "%s"', $this->handleMethod, get_class($helper))
            );
        }

        $this->helpers[$id] = $callback;
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
