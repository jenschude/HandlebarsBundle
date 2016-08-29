<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle;

use JaySDe\HandlebarsBundle\Helper\HelperInterface;

class HandlebarsHelperService implements HandlebarsHelperServiceInterface
{
    private $helpers = [];

    /**
     * @inheritdoc
     */
    public function addHelper($id, $helper)
    {
        if ($helper instanceof HelperInterface) {
            $this->helpers[$id] = [$helper, 'handle'];
        } elseif (is_callable($helper)) {
            $this->helpers[$id] = $helper;
        }
    }

    /**
     * @inheritdoc
     */
    public function getHelperMethods()
    {
        return array_keys($this->helpers);
    }

    /**
     * @inheritdoc
     */
    public function getHelpers()
    {
        return $this->helpers;
    }
}
