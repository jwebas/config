<?php
declare(strict_types=1);


namespace Jwb\Abstracts;


use Jwb\Config;

interface ConfigLoaderInterface
{
    /**
     * @param string|array              $dir
     * @param string|string[]           $patterns
     * @param string|int|string[]|int[] $depth
     *
     * @return Config
     */
    public function load($dir, $patterns, $depth): Config;
}
