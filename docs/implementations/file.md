# File

Save the cache as file to on the filesystem.

You must pass a cache directory to the constructor.

``` php
use Desarrolla2\Cache\File as FileCache;

$cache = new FileCache(sys_get_temp_dir() . '/cache');
```

### Options

| name         | type                              | default        |                                       |
| ------------ | --------------------------------- | -------------- | ------------------------------------- |
| ttl          | int                               | null           | Maximum time to live in seconds       |
| ttl-strategy | string ('embed', 'file', 'mtime') | "embed"        | Strategy to store the TTL             |
| prefix       | string                            | ""             | Key prefix                            |
| filename     | string or callable                | "%s.php.cache" | Filename as sprintf format            |

#### TTL strategy option

The ttl strategy determines how the TTL is stored. Typical filesystems don't
allow custom file properties, so we'll have to use one of these strategies:

| strategy |                                                 |
| -------- | ----------------------------------------------- |
| embed    | Embed the TTL as first line of the file         |
| file     | Create a TTL file in addition to the cache file |
| mtime    | Use [mtime][] + max ttl                         |

The 'mtime' strategy is not PSR-16 compliant, as the TTL passed to the `set()`
method is ignored. Only the `ttl` option for is used on `get()` and `has()`.

[mtime]: https://www.unixtutorial.org/2008/04/atime-ctime-mtime-in-unix-filesystems/

#### Filename option

The `filename` will be parsed using `sprintf` where '%s' is substituted with
the key. The default extension is automatically determined based on the
packer.

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

$callback = new TrieFilename('%s.php.cache', 2);

$cache = (new FileCache(sys_get_temp_dir() . '/cache'))
    ->withOption('filename', $callback);
```

In this case, adding an item with key `foobar` would be create a file at

    /tmp/cache/f/fo/foobar.php.cache

### Packer

By default the [`SerializePacker`](../packers/serialize.md) is used. The
[`NopPacker`](../packers/nop.md) can be used if the values are strings.
Other packers, like the [`JsonPacker`](../packers/json.md) are also
useful with file cache. 
