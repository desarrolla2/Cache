# PhpFile

Save the cache as PHP script to on the filesystem using 
[`var_export`](http://php.net/manual/en/function.var-export.php) when storing
cache and [`include`](http://php.net/manual/en/function.include.php) when
loading cache.

The implementation leverages the PHP engine’s in-memory file caching (opcache)
to cache application data in addition to code. This method is particularly fast
in PHP7.2+ due to opcode cache optimizations.

PHP file caching should primarily be used for arrays and objects. There is no
performance benefit over APCu for storing strings.

[read more][]

``` php
use Desarrolla2\Cache\PhpFile as PhpFileCache;

$cache = new PhpFileCache();
```

### Options

| name      | type               | default        |                                       |
| --------- | ------------------ | -------------- | ------------------------------------- |
| ttl       | int                | null           | Maximum time to live in seconds       |
| prefix    | string             | ""             | Key prefix                            |
| filename  | string or callable | "%s.php"       | Filename as sprintf format            |

#### Filename option

The `filename` will be parsed using `sprintf` where '%s' is substituted with
the key.

Instead of a string, `filename` may also be set to a callable, like a callable
object or closure. In that case the callable will be called to create a
filename as

    $filename = $callable($key);

##### BasicFilename

The library comes with invokable object as callable for the filename. The
`BasicFilename` object works as described above.

##### TrieFilename

The `TrieFilename` object will create a prefix tree directory structure. This
is useful where a lot of cache files would cause to many files in a directory.

Specify the `sprintf` format and the directory level to the constructor when
creating a `TrieFilename` object.

``` php
use Desarrolla2\Cache\File as FileCache;
use Desarrolla2\Cache\File\TrieFilename;

$callback = new TrieFilename('%s.php', 2);

$cache = (new FileCache(sys_get_temp_dir() . '/cache'))
    ->withOption('filename', $callback);
```

In this case, adding an item with key `foobar` would be create a file at

    /tmp/cache/f/fo/foobar.php

### Packer

By default the [`NopPacker`](../packers/nop.md) is used. Other packers should
not be used.

[read more]: https://medium.com/@dylanwenzlau/500x-faster-caching-than-redis-memcache-apc-in-php-hhvm-dcd26e8447ad
