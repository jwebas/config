<?php
declare(strict_types=1);


namespace Jwb;


use Jwb\Abstracts\AbstractConfigLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ConfigLoader extends AbstractConfigLoader
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @inheritdoc
     */
    public function load($dir, $patterns = '*.php', $depth = '<2'): Config
    {
        if (null !== $this->cache) {
            if (!$this->cache->isFresh()) {
                $items = $this->loadFromDir($dir, $patterns, $depth);
                $this->export($items);

                return $this->getConfig($items);
            }

            /** @noinspection PhpIncludeInspection */
            return $this->getConfig(require $this->cache->getPath());
        }

        return $this->getConfig($this->loadFromDir($dir, $patterns, $depth));
    }

    /**
     * @param array $items
     *
     * @return Config
     */
    protected function getConfig(array $items): Config
    {
        return new Config($items);
    }

    /**
     * @param string|array              $dir
     * @param string|string[]           $patterns
     * @param string|int|string[]|int[] $depth
     *
     * @return array
     */
    protected function loadFromDir($dir, $patterns, $depth): array
    {
        $this->addDirectoryResource($dir);

        $items = [];
        $files = Finder::create()
            ->in($dir)
            ->files()
            ->name($patterns)
            ->depth($depth);

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $key = $file->getBasename('.php');

            /** @noinspection PhpIncludeInspection */
            $items[$key] = include $file->getRealPath();

            $this->addFileResource($file->getRealPath());
        }

        $this->parameters = $items;
        $this->replacePlaceholders($items);

        return $items;
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
        if (\is_string($string)) {
            if (preg_match('/^%([0-9A-Za-z._-]+)%$/', $string, $matches)) {
                $string = array_get($this->parameters, $matches[1], $matches[0]);
            } else {
                $string = preg_replace_callback('/%([0-9A-Za-z._-]+)%/', function ($matches) {

                    $string = array_get($this->parameters, $matches[1], $matches[0]);

                    if (\is_array($string) || \is_object($string)) {
                        return $matches[0];
                    }

                    return $string;
                }, $string);
            }
        }
    }
}
