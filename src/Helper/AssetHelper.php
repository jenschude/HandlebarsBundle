<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Helper;

use Symfony\Component\Asset\Packages;

class AssetHelper implements HelperInterface
{
    private $packages;

    public function __construct(Packages $packages)
    {
        $this->packages = $packages;
    }
    
    public function handle($context, $options)
    {
        $packageName = isset($options['hash']['package']) ? $options['hash']['package'] : null;

        return $this->packages->getUrl($context, $packageName);
    }
}
