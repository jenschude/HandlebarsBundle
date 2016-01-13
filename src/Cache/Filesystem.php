<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Cache;


class Filesystem
{
    const FORCE_BYTECODE_INVALIDATION = 1;

    private $directory;
    private $options;

    /**
     * @param $directory string The root cache directory
     * @param $options   int    A set of options
     */
    public function __construct($directory, $options = 0)
    {
        $this->directory = $directory;
        $this->options = $options;
    }

    public function generateKey($name)
    {
        $hash = hash('sha256', $name);

        return $this->directory.'/'.$hash[0].$hash[1].'/'.$hash.'.php';
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
    public function write($key, $content)
    {
        $dir = dirname($key);
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Unable to create the cache directory (%s).', $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new \RuntimeException(sprintf('Unable to write in the cache directory (%s).', $dir));
        }

        $tmpFile = tempnam($dir, basename($key));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $key)) {
            @chmod($key, 0666 & ~umask());

            if (self::FORCE_BYTECODE_INVALIDATION == ($this->options & self::FORCE_BYTECODE_INVALIDATION)) {
                // Compile cached file into bytecode cache
                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate($key, true);
                } elseif (function_exists('apc_compile_file')) {
                    apc_compile_file($key);
                }
            }

            return;
        }

        throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $key));
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
