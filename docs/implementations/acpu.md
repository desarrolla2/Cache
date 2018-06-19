# Apcu

Use [APCu cache](http://php.net/manual/en/book.apcu.php) to cache to shared
memory.

``` php
use Desarrolla2\Cache\Apcu as ApcuCache;

$cache = new ApcuCache();
```

_Note: by default APCu uses the time at the beginning of a request for ttl. In
some cases, like with a long running script, this can be a problem. You can
change this behaviour `ini_set('apc.use_request_time', false)`._

### Options

| name      | type      | default |                                       |
| --------- | ----      | ------- | ------------------------------------- |
| ttl       | int       | null    | Maximum time to live in seconds       |
| prefix    | string    | ""      | Key prefix                            |

### Packer

By default the [`NopPacker`](../packers/nop.md) is used.
