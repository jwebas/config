<?php
declare(strict_types=1);


namespace Jwebas\Config;


use ArrayAccess;
use Jwebas\Utils\Arr;

class Config implements ArrayAccess
{
    /**
     * All items
     *
     * @var array
     */
    protected $items = [];

    /**
     * Config constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Determine if the given configuration value exists using "dot" notation.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }

    /**
     * Get a configuration option.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /*
     * ------------------------
     * ArrayAccess methods
     * ------------------------
     *
     * */

    /**
     * Get the specified configuration value using "dot" notation.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }

    /**
     * Set a configuration option.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Set a given configuration value using "dot" notation.
     *
     * @param array|string $key
     * @param mixed        $value
     */
    public function set($key, $value = null): void
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $arrayKey => $arrayValue) {
            Arr::set($this->items, $arrayKey, $arrayValue);
        }
    }

    /**
     * Unset a configuration option.
     *
     * @param string $key
     */
    public function offsetUnset($key): void
    {
        $this->set($key);
    }
}
