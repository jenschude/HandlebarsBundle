<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Loader;


use JaySDe\HandlebarsBundle\Error\LoaderException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class FilesystemLoader
{
    /** Identifier of the main namespace. */
    const MAIN_NAMESPACE = '__main__';

    protected $locator;
    protected $parser;

    protected $paths = [];
    protected $cache = [];
    protected $errorCache = [];

    /**
     * FilesystemLoader constructor.
     * @param FileLocatorInterface $locator
     * @param TemplateNameParserInterface $parser
     */
    public function __construct(FileLocatorInterface $locator, TemplateNameParserInterface $parser)
    {
        $this->locator = $locator;
        $this->parser = $parser;
        $this->setPaths([]);
    }

    /**
     * Returns the paths to the templates.
     *
     * @param string $namespace A path namespace
     *
     * @return array The array of paths where to look for templates
     */
    public function getPaths($namespace = self::MAIN_NAMESPACE)
    {
        return isset($this->paths[$namespace]) ? $this->paths[$namespace] : [];
    }

    /**
     * Returns the path namespaces.
     *
     * The main namespace is always defined.
     *
     * @return array The array of defined namespaces
     */
    public function getNamespaces()
    {
        return array_keys($this->paths);
    }

    /**
     * Sets the paths where templates are stored.
     *
     * @param string|array $paths     A path or an array of paths where to look for templates
     * @param string       $namespace A path namespace
     */
    public function setPaths($paths, $namespace = self::MAIN_NAMESPACE)
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }

        $this->paths[$namespace] = [];
        foreach ($paths as $path) {
            $this->addPath($path, $namespace);
        }
    }

    /**
     * Adds a path where templates are stored.
     *
     * @param string $path      A path where to look for templates
     * @param string $namespace A path name
     *
     * @throws LoaderException
     */
    public function addPath($path, $namespace = self::MAIN_NAMESPACE)
    {
        // invalidate the cache
        $this->cache = $this->errorCache = [];

        if (!is_dir($path)) {
            throw new LoaderException(sprintf('The "%s" directory does not exist.', $path));
        }

        $this->paths[$namespace][] = rtrim($path, '/\\');
    }

    /**
     * Prepends a path where templates are stored.
     *
     * @param string $path      A path where to look for templates
     * @param string $namespace A path name
     *
     * @throws LoaderException
     */
    public function prependPath($path, $namespace = self::MAIN_NAMESPACE)
    {
        // invalidate the cache
        $this->cache = $this->errorCache = [];

        if (!is_dir($path)) {
            throw new LoaderException(sprintf('The "%s" directory does not exist.', $path));
        }

        $path = rtrim($path, '/\\');

        if (!isset($this->paths[$namespace])) {
            $this->paths[$namespace][] = $path;
        } else {
            array_unshift($this->paths[$namespace], $path);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($template)
    {
        $name = $this->normalizeName($template);

        if (isset($this->cache[$name])) {
            return true;
        }

        try {
            return false !== $this->findTemplate($name, false);
        } catch (LoaderException $exception) {
            return false;
        }
    }

    protected function parseName($name, $default = self::MAIN_NAMESPACE)
    {
        if (isset($name[0]) && '@' == $name[0]) {
            if (false === $pos = strpos($name, '/')) {
                throw new LoaderException(sprintf('Malformed namespaced template name "%s" (expecting "@namespace/template_name").', $name));
            }

            $namespace = substr($name, 1, $pos - 1);
            $shortname = substr($name, $pos + 1);

            return array($namespace, $shortname);
        }

        return array($default, $name);
    }

    protected function normalizeName($name)
    {
        return preg_replace('#/{2,}#', '/', str_replace('\\', '/', (string) $name));
    }

    protected function findTemplate($template, $throw = true)
    {
        $normalizedName = $this->normalizeName($template);

        if (isset($this->cache[$normalizedName])) {
            return $this->cache[$normalizedName];
        }

        list($namespace, $shortName) = $this->parseName($normalizedName);

        if (!$this->validateTemplate($normalizedName, $namespace)) {
            if ($throw) { throw new LoaderException($this->errorCache[$normalizedName]); }
            return false;
        }

        return $this->searchTemplate($template, $normalizedName, $shortName, $namespace, $throw);
    }

    private function searchTemplate($template, $name, $shortName, $namespace, $throw) {
        foreach ($this->paths[$namespace] as $path) {
            if (is_file($path.'/'.$shortName)) {
                if (false !== $realpath = realpath($path.'/'.$shortName)) {
                    return $this->cache[$name] = $realpath;
                }

                return $this->cache[$name] = $path.'/'.$shortName;
            }
        }

        if (!$template = $this->locateTemplate($template, $name, $namespace)) {
            if ($throw) { throw new LoaderException($this->errorCache[$name]); }
            return false;
        }
        return $template;
    }

    private function validateTemplate($name, $namespace)
    {
        if (isset($this->errorCache[$name])) {
            return false;
        }

        if (!isset($this->paths[$namespace])) {
            $this->errorCache[$name] = sprintf('There are no registered paths for namespace "%s".', $namespace);

            return false;
        }

        return true;
    }

    private function locateTemplate($template, $name, $namespace)
    {
        try {
            $template = $this->parser->parse($template);
            $realpath = $this->locator->locate($template);
            if (false !== $realpath && null !== $realpath) {
                return $this->cache[$name] = $realpath;
            }
        } catch (\Exception $e) {
            // catch locator not found exceptions
        }

        $this->errorCache[$name] = sprintf(
            'Unable to find template "%s" (looked into: %s).',
            $name,
            implode(', ', $this->paths[$namespace])
        );
        return false;
    }
}
