<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle;


use JaySDe\HandlebarsBundle\Cache\Filesystem;
use JaySDe\HandlebarsBundle\Error\LoaderException;
use JaySDe\HandlebarsBundle\Loader\FilesystemLoader;
use LightnCandy\LightnCandy;
use Symfony\Component\Config\Resource\FileResource;

class HandlebarsEnvironment
{
    protected $options;

    /**
     * @var Filesystem
     */
    protected $cache;
    /**
     * @var FilesystemLoader
     */
    protected $loader;

    protected $extensions = [];
    protected $autoReload;
    protected $debug;
    private $profiler;

    private $helper;

    public function __construct(
        FilesystemLoader $loader,
        HandlebarsHelper $helper,
        $options = [],
        Filesystem $cache,
        HandlebarsProfileExtension $profiler
    )
    {
        $this->loader = $loader;
        $this->partials = $partials = new \ArrayObject();
        $this->helper = $helper;
        $flags = (LightnCandy::FLAG_BESTPERFORMANCE |
            LightnCandy::FLAG_HANDLEBARSJS |
            LightnCandy::FLAG_RUNTIMEPARTIAL |
            LightnCandy::FLAG_HANDLEBARSLAMBDA |
            LightnCandy::FLAG_EXTHELPER |
            LightnCandy::FLAG_ERROR_EXCEPTION) &
            ~LightnCandy::FLAG_STANDALONEPHP
        ;
        $this->options = array_merge([
            'auto_reload' => null,
            'debug' => true,
            'flags' => $flags,
            'helpers' => $helper->getHelperMethods(),
            'partialresolver' => function ($cx, $name) use ($loader, &$partials) {
                $extension = false;
                if ($loader->exists($name . '.handlebars')) {
                    $extension = '.handlebars';
                } else if ($loader->exists($name . '.hbs')) {
                    $extension = '.hbs';
                }
                if ($extension === false) {
                    return null;
                }
                $partials[] = new FileResource($loader->getCacheKey($name . $extension));
                return $loader->getSource($name . $extension);
            },
        ], $options);
        $this->debug = (bool) $this->options['debug'];
        $this->autoReload = null === $this->options['auto_reload'] ? $this->debug : (bool) $this->options['auto_reload'];
        $this->cache = $cache;
        $this->profiler = $profiler;
    }

    public function compile($name)
    {
        $source = $this->getLoader()->getSource($name);
        $cacheKey = $this->getCacheFilename($name);

        $phpStr = '';
        try {
            $this->partials->exchangeArray([new FileResource($this->getLoader()->getCacheKey($name))]);
            $phpStr = LightnCandy::compile($source, $this->options);
        } catch (\Exception $e) {
            throw new LoaderException($e->getMessage());
        }
        $this->cache->write($cacheKey, '<?php // ' . $name . PHP_EOL . $phpStr, $this->partials->getArrayCopy());

        return $phpStr;
    }

    public function render($name, array $context = [])
    {
        $renderer = $this->loadTemplate($name);

        $templateProfile = new \Twig_Profiler_Profile($name, \Twig_Profiler_Profile::TEMPLATE, $name);
        $this->profiler->enter($templateProfile);
        $html = $renderer($context, ['helpers' => $this->helper->getHelpers()]);
        $this->profiler->leave($templateProfile);

        return $html;
    }

    public function getCacheFilename($name)
    {
        $key = $this->cache->generateKey($name);

        return !$key ? false : $key;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @param $templateName
     * @return callable
     */
    public function loadTemplate($templateName)
    {
        $name = (string)$templateName;
        $cacheKey = $this->getCacheFilename($name);
        if (!$this->isAutoReload() && file_exists($cacheKey)) {
            return $this->cache->load($cacheKey);
        } else if ($this->isAutoReload() && $this->cache->isFresh($cacheKey)) {
            return $this->cache->load($cacheKey);
        }
        $this->compile($name);

        return $this->cache->load($cacheKey);
    }

    /**
     * Checks if the auto_reload option is enabled.
     *
     * @return bool true if auto_reload is enabled, false otherwise
     */
    public function isAutoReload()
    {
        return $this->autoReload;
    }
}
