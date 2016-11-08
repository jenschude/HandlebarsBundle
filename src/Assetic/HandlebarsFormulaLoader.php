<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Assetic;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;
use JaySDe\HandlebarsBundle\HandlebarsEnvironment;
use Psr\Log\LoggerInterface;

class HandlebarsFormulaLoader implements FormulaLoaderInterface
{
    public function load(ResourceInterface $resource)
    {
        return [];
    }
}
