# Redis

Cache using a [redis server](https://redis.io/). Redis is an open source,
in-memory data structure store, used as a database, cache and message broker.

Uses the [Redis PHP extension](https://github.com/phpredis/phpredis).

You must provide a `Redis` object to the constructor.

```php
use Desarrolla2\Cache\Redis as RedisCache;
use Redis;

$client = new Redis();
$cache = new RedisCache($client);
```

### Installation

Requires the [`redis`](https://github.com/phpredis/phpredis) PHP extension from PECL.

    pickle install redis

### Options

| name      | type      | default |                                       |
| --------- | ----      | ------- | ------------------------------------- |
| ttl       | int       | null    | Maximum time to live in seconds       |
| prefix    | string    | ""      | Key prefix                            |

### Packer

By default the [`SerializePacker`](../packers/serialize.md) is used.
