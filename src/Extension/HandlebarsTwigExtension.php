<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Extension;

use JaySDe\HandlebarsBundle\HandlebarsEngine;

class HandlebarsTwigExtension extends \Twig_Extension
{
    private $engine;

    public function __construct(HandlebarsEngine $environment)
    {
        $this->engine = $environment;

    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_hbs', [$this, 'renderHandlebars'])
        ];
    }

    public function renderHandlebars($name, array $data = [])
    {
        return $this->engine->render($name, $data);
    }

    public function getName()
    {
        return 'HandlebarsTwigExtension';
    }
}
