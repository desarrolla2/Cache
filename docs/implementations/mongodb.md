# Mongo

Use it to store the cache in a Mongo database. Requires the mongodb extension
and the [mongodb/mongodb](https://github.com/mongodb/mongo-php-library)
library.

You must pass a `MongoDB\Collection` object to the cache constructor.

``` php
<?php

use Desarrolla2\Cache\Mongo as MongoCache;
use MongoDB\Client;

$client = new Client('mongodb://localhost:27017');
$database = $client->selectDatabase('mycache');
$collection = $database->selectCollection('cache');

$cache = new MongoCache($collection);
```

MonoDB will always automatically create the database and collection if needed. 

### Options

| name       | type      | default |                                       |
| ---------  | ----      | ------- | ------------------------------------- |
| initialize | bool      | true    | Enable auto-initialize                |
| ttl        | int       | null    | Maximum time to live in seconds       |
| prefix     | string    | ""      | Key prefix                            |

#### Initialize option

If `initialize` is enabled, the cache implementation will automatically create
a [ttl index](https://docs.mongodb.com/manual/core/index-ttl/). In production
it's better to disable auto-initialization and create the ttl index explicitly
when setting up the database. This prevents a `createIndex()` call on each
request. 

### Packer

By default the [`MongoDBBinaryPacker`](../packers/mongodbbinary.md) is used. It
serializes the data and stores it in a [Binary BSON variable](http://php.net/manual/en/class.mongodb-bson-binary.php). 
If the data is a UTF-8 string of simple array or stdClass object, it may be
useful to use the [`NopPacker`](../packers/nop.md) instead.
