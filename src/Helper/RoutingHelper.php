<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Helper;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RoutingHelper implements HelperInterface
{
    private $generator;
    private $type;

    public function __construct(UrlGeneratorInterface $generator, $type)
    {
        $this->generator = $generator;
        $this->type = strtolower($type);
    }
    
    public function handle($context, $options)
    {
        $options = isset($options['hash']) ? $options['hash'] : [];
        $relative = isset($options['relative']) ? $options['relative'] : false;
        switch ($this->type) {
            case 'path':
                return $this->getPath($context, $options, $relative);
            case 'url':
                return $this->getUrl($context, $options, $relative);
            default:
                throw new \InvalidArgumentException();
        }
    }

    private function getPath($name, $parameters = array(), $relative = false)
    {
        return $this->generator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    private function getUrl($name, $parameters = array(), $schemeRelative = false)
    {
        return $this->generator->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
