<?php
declare(strict_types=1);


namespace Jwebas\Config\Loaders\Abstracts;


use Jwebas\Utils\Arr;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;

abstract class AbstractConfigLoader
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
     * @var array
     */
    protected $parameters = [];

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

    /**
     * Parses the configuration and replaces placeholders with the corresponding parameters values.
     *
     * @param array $items
     */
    protected function replacePlaceholders(array &$items): void
    {
        array_walk_recursive($items, [$this, 'replaceStringPlaceholders']);
    }

    /**
     * Replaces configuration placeholders with the corresponding parameters values.
     *
     * @param $string
     */
    protected function replaceStringPlaceholders(&$string): void
    {
        if (is_string($string)) {
            if (preg_match('/^%([0-9A-Za-z._-]+)%$/', $string, $matches)) {
                $string = Arr::get($this->parameters, $matches[1], $matches[0]);
            } else {
                $string = preg_replace_callback('/%([0-9A-Za-z._-]+)%/', function ($matches) {

                    $string = Arr::get($this->parameters, $matches[1], $matches[0]);

                    if (is_array($string) || is_object($string)) {
                        return $matches[0];
                    }

                    return $string;
                }, $string);
            }
        }
    }
}
