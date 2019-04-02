# Jwebas Config

Config is a files configuration loader that supports PHP files.

## Requirements

Config requires PHP 7.1.3+.

## Usage

### Create loader

```php
use Jwebas\Config\Loader\ConfigLoader;

// Loader without cache and debug.
$configLoader = new ConfigLoader();

// Loader with cache based on Symfony Config(https://symfony.com/doc/current/components/config/caching.html).
$configLoader = new ConfigLoader($cachePath);

// Loader with cache and debug.
$configLoader = new ConfigLoader($cachePath, true);
```

### Add resource(s)

```php
$configLoader
    ->addDirectory($path_1)
    ->addDirectory($path_2, [
        'patterns' => 'config.php',
        'depth'    => 1,
        'callback' => static function (array $data, Symfony\Component\Finder\SplFileInfo $file) { return $name; },
    ]);
```

### Get config

```php
$config = $configLoader->load();
```

### Use config

```php
// Get all of the configuration items.
$items = $config->all(): array

// Get the specified configuration value using "dot" notation.
$item = $config->get(string $key, $default = null): mixed

// Determine if the given configuration value exists using "dot" notation.
$exists = $config->has(string $key): bool

// Set a given configuration value using "dot" notation.
$config->set($key, $value = null): void
```

### Links
* Symfony Config - https://symfony.com/doc/current/components/config.html
* Symfony Finder - https://symfony.com/doc/current/components/finder.html
* Illuminate Config (Laravel) - https://github.com/illuminate/config

## License

The MIT License (MIT).
