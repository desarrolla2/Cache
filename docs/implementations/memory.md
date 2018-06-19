# Memory

Store the cache in process memory _(in other words in an array)_. Cache Memory
is removed when the PHP process exist. Also it is not shared between different
processes.

``` php
use Desarrolla2\Cache\Memory as MemoryCache;

$cache = new MemoryCache();
```

### Options

| name      | type      | default |                                       |
| --------- | ----      | ------- | ------------------------------------- |
| ttl       | int       | null    | Maximum time to live in seconds       |
| limit     | int       | null    | Maximum items in cache                |
| prefix    | string    | ""      | Key prefix                            |

### Packer

By default the [`SerializePacker`](../packers/serialize.md) is used.
