# Memcached

Store cache to [Memcached](https://memcached.org/). Memcached is a high
performance distributed caching system.

``` php
use Desarrolla2\Cache\Memcached as MemcachedCache;
use Memcached;

$server = new Memcached();
// configure it here

$cache = new MemcachedCache($server);
```

This implementation uses the [memcached](https://php.net/memcached) php
extension. The (alternative) memcache extension is not supported.

### Options

| name      | type      | default |                                       |
| --------- | ----      | ------- | ------------------------------------- |
| ttl       | int       | null    | Maximum time to live in seconds       |
| prefix    | string    | ""      | Key prefix                            |

### Packer

By default the [`NopPacker`](../packers/nop.md) is used.
