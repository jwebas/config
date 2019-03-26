<?php
declare(strict_types=1);


namespace Jwebas\Config\Loaders;


use Jwebas\Config\Config;
use Jwebas\Config\Loaders\Abstracts\AbstractConfigLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DirectoryLoader extends AbstractConfigLoader
{
    /**
     * @param string|array              $dir
     * @param string|string[]           $patterns
     * @param string|int|string[]|int[] $depth
     *
     * @return Config
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
}
