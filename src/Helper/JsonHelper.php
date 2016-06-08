<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Helper;

class JsonHelper implements HelperInterface
{
    public static function handle($context, $options)
    {
        return json_encode($context);
    }
}
