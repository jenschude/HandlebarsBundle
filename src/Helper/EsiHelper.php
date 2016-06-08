<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Helper;

use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class EsiHelper implements HelperInterface
{
    /**
     * @var FragmentHandler
     */
    private static $handler;

    public function __construct(FragmentHandler $handler)
    {
        self::$handler = $handler;
    }

    public static function handle($context, $options)
    {
        $handler = self::$handler;
        $result = new \LightnCandy\SafeString($handler->render($context, 'esi', $options));
        return $result;
    }
}
