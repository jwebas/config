# Config

Config is a file configuration loader that supports PHP files.

## Requirements

Config requires PHP 7.1.3+.

## Usage

### Create loader

```php
use Jwb\ConfigLoader;

// Loader without cache and debug.
$configLoader = new ConfigLoader();

// Loader with cache based on [Symfony Config](https://symfony.com/doc/current/components/config/caching.html).
$configLoader = new ConfigLoader($cachePath);

// Loader with cache and debug.
$configLoader = new ConfigLoader($cachePath, true);
```

### Get config

```php
// Load all supported files in a directory.
$config = $configLoader->load(__DIR__ . '/config', $patterns = '*.php', $depth = '<2');

// Load all supported files in multiple directories.
$config = $configLoader->load([__DIR__ . '/config', __DIR__ . '/config1']);

// Load all supported files in a directory with depth <2 and named config.php.
$config = $configLoader->load(__DIR__ . '/config', 'config.php', , $depth = '<2');
```

### Use config

```php
// Get the specified configuration value using "dot" notation.
$item = $config->get(string $key, $default = null): mixed

// Determine if the given configuration value exists using "dot" notation.
$exists = $config->has(string $key): bool

// Set a given configuration value using "dot" notation.
$config->set($key, $value = null): void

// Get all of the configuration items.
$items = $config->all(): array
```

### Links
* Symfony Config - https://symfony.com/doc/current/components/config.html
* Symfony Finder - https://symfony.com/doc/current/components/finder.html
* Illuminate Config (Laravel) - https://github.com/illuminate/config

## License

The MIT License (MIT).
