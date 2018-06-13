<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Tests\Extension;

use JaySDe\HandlebarsBundle\HandlebarsEnvironment;

class HandlebarsTwigExtension extends \Twig_Extension
{
    private $environment;

    public function __construct(HandlebarsEnvironment $environment)
    {
        $this->environment = $environment;

    }
    public function getFunctions()
    {
        return [
            new \Twig_Function('render_hbs', [$this, 'renderHandlebars'])
        ];
    }

    public function renderHandlebars($name, array $data = [])
    {
        return $this->environment->render($name, $data);
    }
}
