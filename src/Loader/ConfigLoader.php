<?php declare(strict_types=1);


namespace Jwebas\Config\Loader;


use Closure;
use Jwebas\Config\Config;
use Jwebas\Config\Loaders\Abstracts\AbstractConfigLoader;
use Jwebas\Utils\Arr;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ConfigLoader extends AbstractConfigLoader
{
    /**
     * @var array
     */
    protected $directories = [];

    /**
     * @var array
     */
    protected static $defaultParams = [
        'callback' => 'filename',
        'patterns' => '*.php',
        'depth'    => '< 2',
    ];

    /**
     * @param string $path
     * @param array  $params
     *
     * @return $this
     */
    public function addDirectory(string $path, array $params = []): self
    {
        $this->directories[] = [
            'path'   => $path,
            'params' => array_merge(static::$defaultParams, $params),
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function load(): Config
    {
        if (null !== $this->cache) {
            if (!$this->cache->isFresh()) {
                $items = $this->loadConfig();
                $this->export($items);

                return $this->getConfig($items);
            }

            /** @noinspection PhpIncludeInspection */
            return $this->getConfig(require $this->cache->getPath());
        }

        return $this->getConfig($this->loadConfig());
    }

    /**
     * @return array
     */
    protected function loadConfig(): array
    {
        $items = [];

        foreach ($this->directories as $directory) {
            $path = $directory['path'];
            $callback = $directory['params']['callback'];

            $this->addDirectoryResource($path);

            $files = Finder::create()
                ->in($path)
                ->files()
                ->name($directory['params']['patterns'])
                ->depth($directory['params']['depth']);

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                /** @noinspection PhpIncludeInspection */
                $data = include $file->getRealPath();
                $this->addFileResource($file->getRealPath());

                if ($callback instanceof Closure) {
                    $key = $callback($data, $file);
                } else {
                    $key = $file->getBasename('.php');
                }

                Arr::set($items, $key, $data);
            }
        }

        $this->parameters = $items;
        $this->replacePlaceholders($items);

        return $items;
    }
}
