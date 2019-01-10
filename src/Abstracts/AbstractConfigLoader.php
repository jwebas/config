<?php
declare(strict_types=1);


namespace Jwb\Abstracts;


use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;

abstract class AbstractConfigLoader implements ConfigLoaderInterface
{
    /**
     * @var ConfigCache|null
     */
    protected $cache;

    /**
     * @var array
     */
    protected $resources = [];

    /**
     * AbstractConfigLoader constructor.
     *
     * @param string|null $cachePath
     * @param bool        $debug
     */
    public function __construct($cachePath = null, $debug = false)
    {
        if (null !== $cachePath) {
            $this->cache = new ConfigCache($cachePath, $debug);
        }
    }

    /**
     * Add file to resource
     *
     * @param string $resource
     */
    protected function addFileResource(string $resource): void
    {
        $this->resources[] = new FileResource($resource);
    }

    /**
     * Add directory to resource
     *
     * @param string      $resource
     * @param string|null $pattern
     */
    protected function addDirectoryResource(string $resource, string $pattern = null): void
    {
        $this->resources[] = new DirectoryResource($resource, $pattern);
    }

    /**
     * Export items to file
     *
     * @param array $items
     */
    protected function export(array $items): void
    {
        $content = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($items, true) . ';' . PHP_EOL;
        $this->cache->write($content, $this->resources);
    }
}
