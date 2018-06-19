# Predis

Cache using a [redis server](https://redis.io/). Redis is an open source,
in-memory data structure store, used as a database, cache and message broker.  

You must provide a `Predis\Client` object to the constructor.

```php
use Desarrolla2\Cache\Predis as PredisCache;
use Predis\Client as PredisClient;

$client = new PredisClient('tcp://localhost:6379');
$cache = new PredisCache($client);
```

### Installation

Requires the [`predis`](https://github.com/nrk/predis/wiki) library.

    composer require predis/predis

### Options

| name      | type      | default |                                       |
| --------- | ----      | ------- | ------------------------------------- |
| ttl       | int       | null    | Maximum time to live in seconds       |
| prefix    | string    | ""      | Key prefix                            |

### Packer

By default the [`SerializePacker`](../packers/serialize.md) is used.
