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
    private $handler;

    public function __construct(FragmentHandler $handler)
    {
        $this->handler = $handler;
    }

    public function handle($context, $options)
    {
        $handler = $this->handler;
        $result = new \LightnCandy\SafeString($handler->render($context, 'esi', $options));
        return $result;
    }
}
