<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle\Tests\Cache;


use JaySDe\HandlebarsBundle\Cache\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;

class FilesystemTest extends TestCase
{
    protected function tearDown()
    {
        if (file_exists(__DIR__ . '/test')) {
            unlink(__DIR__ . '/test');
        }
        if (file_exists(__DIR__ . '/test.meta')) {
            unlink(__DIR__ . '/test.meta');
        }
    }

    public function testGenerateKey()
    {
        $cache = new Filesystem(__DIR__);

        $key = $cache->generateKey('test');

        $this->assertSame(2, strlen(basename(dirname($key))));
        $this->assertStringEndsWith('.php', $key);
        $this->assertNotSame('test', basename($key, '.php'));
    }

    public function testIsNotFresh()
    {
        $cache = new Filesystem(__DIR__, true);
        $this->assertFalse($cache->isFresh(__DIR__ . '/test'));
    }

    public function testWrite()
    {
        $cache = new Filesystem(__DIR__, true);
        $time = date('c');
        $cache->write(__DIR__ . '/test', $time, [new FileResource(__DIR__ . '/../Fixtures/Resources/views/main.hbs')]);
        $this->assertFileExists(__DIR__ . '/test');
        $this->assertFileExists(__DIR__ . '/test.meta');
    }

    public function testIsFresh()
    {
        $cache = new Filesystem(__DIR__, true);
        $time = date('c');
        $cache->write(__DIR__ . '/test', $time, [new FileResource(__DIR__ . '/../Fixtures/Resources/views/main.hbs')]);
        $this->assertTrue($cache->isFresh(__DIR__ . '/test'));
    }

    public function testLoad()
    {
        $cache = new Filesystem(__DIR__, true);
        $time = date('c');
        $cache->write(
            __DIR__ . '/test',
            '<?php return "' . $time . '";',
            [new FileResource(__DIR__ . '/../Fixtures/Resources/views/main.hbs')]
        );
        $this->assertSame($time, $cache->load(__DIR__ . '/test'));
    }
}
