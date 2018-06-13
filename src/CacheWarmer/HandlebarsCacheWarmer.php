<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\CacheWarmer;

use JaySDe\HandlebarsBundle\HandlebarsEngine;
use JaySDe\HandlebarsBundle\HandlebarsEnvironment;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class HandlebarsCacheWarmer implements CacheWarmerInterface
{
    private $environment;
    private $finder;
    private $logger;

    /**
     * Constructor.
     *
     * @param HandlebarsEngine $environment The handlebars engine
     * @param TemplateFinderInterface $finder The template paths cache warmer
     * @param LoggerInterface $logger
     */
    public function __construct(
        HandlebarsEnvironment $environment,
        TemplateFinderInterface $finder,
        LoggerInterface $logger = null
    ) {
        // We don't inject the HandlebarsEngine directly as it depends on the
        // template locator (via the loader) which might be a cached one.
        // The cached template locator is available once the TemplatePathsCacheWarmer
        // has been warmed up
        $this->environment = $environment;
        $this->finder = $finder;
        $this->logger = $logger;
    }
    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $environment = $this->environment;
        foreach ($this->finder->findAllTemplates() as $template) {
            if (!in_array($template->get('engine'), ['hbs', 'handlebars'])) {
                continue;
            }
            try {
                $environment->compile($template);
            } catch (\Exception $e) {
                // problem during compilation, log it and give up
                if ($this->logger instanceof LoggerInterface) {
                    $this->logger->warning(
                        sprintf(
                            'Failed to compile Handlebars template "%s": "%s"',
                            (string) $template,
                            $e->getMessage()
                        )
                    );
                }
            }
        }
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * @return Boolean always true
     */
    public function isOptional()
    {
        return true;
    }
}
