<?php declare(strict_types=1);


namespace Jwebas\Config\Loaders\Abstracts;


use Jwebas\Config\Config;

interface ConfigLoaderInterface
{
    /**
     * @return Config
     */
    public function load(): Config;
}
