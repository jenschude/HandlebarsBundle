<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Assetic;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;

class HandlebarsFormulaLoader implements FormulaLoaderInterface
{
    public function load(ResourceInterface $resource)
    {
        return [];
    }
}
