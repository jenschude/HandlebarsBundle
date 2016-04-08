<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Cache;


use Symfony\Component\Config\ConfigCache;

class Filesystem
{
    private $directory;
    private $debug;

    /**
     * @param $directory string The root cache directory
     * @param bool $debug
     */
    public function __construct($directory, $debug = false)
    {
        $this->directory = $directory;
        $this->debug = $debug;
    }

    public function generateKey($name)
    {
        $hash = hash('sha256', $name);

        return $this->directory.'/'.$hash[0].$hash[1].'/'.$hash.'.php';
    }

    public function isFresh($key)
    {
        $cache = new ConfigCache($key, $this->debug);
        return $cache->isFresh();
    }

    /**
     * {@inheritdoc}
     */
    public function load($key)
    {
        return @include $key;
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $content, $resources)
    {
        $cache = new ConfigCache($key, $this->debug);
        $cache->write($content, $resources);

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($key)
    {
        if (!file_exists($key)) {
            return 0;
        }

        return (int) @filemtime($key);
    }
}
