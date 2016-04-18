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
    private $handlebars;
    private $logger;

    public function __construct(HandlebarsEnvironment $handlebars, LoggerInterface $logger = null)
    {
        $this->handlebars = $handlebars;
        $this->logger = $logger;
    }

    public function load(ResourceInterface $resource)
    {
        return [];
    }
}
