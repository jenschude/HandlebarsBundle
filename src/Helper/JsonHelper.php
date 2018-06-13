<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Helper;

class JsonHelper implements HelperInterface
{
    public function handle($context, $options)
    {
        return json_encode($context);
    }
}
