# Cache [<img alt="SensioLabsInsight" src="https://insight.sensiolabs.com/projects/5f139261-1ac1-4559-846a-723e09319a88/small.png" align="right">](https://insight.sensiolabs.com/projects/5f139261-1ac1-4559-846a-723e09319a88)

A simple cache library, implementing the [PSR-16](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md) standard.


[![Latest version][ico-version]][link-packagist]
[![Latest version][ico-pre-release]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-coveralls]][link-coveralls]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Sensiolabs Insight][ico-sensiolabs]][link-sensiolabs]
[![Total Downloads][ico-downloads]][link-downloads]
[![Today Downloads][ico-today-downloads]][link-downloads]
[![Gitter][ico-gitter]][link-gitter]


## Installation

```
composer require desarrolla2/cache
```


## Usage


``` php
<?php

use Desarrolla2\Cache\NotCache;

$cache = new NotCache();

$cache->set('key', 'myKeyValue', 3600);

// later ...

echo $cache->get('key');

```

## Cache implementations

### Apcu

Use [APCu cache](http://php.net/manual/en/book.apcu.php) to cache to shared
memory.

``` php
<?php
    
use Desarrolla2\Cache\Apcu as ApcuCache;

$cache = new ApcuCache();
$cache->setOption('ttl', 3600);
$cache->setOption('pack-ttl', true);

```

If the `pack-ttl` option is set to false, the cache will rely on APCu's TTL and
not verify the TTL itself.

### File

Save the cache as file to on the filesystem

``` php
<?php
    
use Desarrolla2\Cache\File as FileCache;

$cacheDir = '/tmp';
$cache = new FileCache($cacheDir);
$cache->setOption('ttl', 3600);
$cache->setOption('pack-ttl', true);

```

If the `pack-ttl` option is set to false, the cache file will only contain the
cached value. The TTL is written a file suffixed with `.ttl`.


### Memcache

Store cache to [Memcached](https://memcached.org/). Memcached is a high
performance distributed caching system.

``` php
<?php
    
use Desarrolla2\Cache\Memcache as MemcacheCache;

$cache = new MemcacheCache();

```

You can config your connection before


``` php
<?php
    
use Desarrolla2\Cache\Memcache as MemcacheCache;
use \Memcache as Backend

$backend = new Backend();
// configure it here

$cache = new MemcacheCache($backend))
```

### Memcached

Is the same like memcache adapter.

### Memory

Store the cache in process memory. Cache Memory is removed when the PHP process
exist. Also it is not shared between different processes.

Memory cache have a option "limit", that limit the max items in cache.

``` php
<?php
    
use Desarrolla2\Cache\Memory as MemoryCache;

$cache = new MemoryCache();
$cache->setOption('ttl', 3600);
$cache->setOption('limit', 200);

```

### Mongo

Use it to store the cache in a Mongo database. Requires the
[(legacy) mongo extension](http://php.net/mongo) or the
[mongodb/mongodb](https://github.com/mongodb/mongo-php-library) library.

You may pass either a database or collection object to the constructor. If a
database object is passed, the `items` collection within that DB is used.

``` php
<?php

use Desarrolla2\Cache\Mongo as MongoCache;

$client = new MongoClient($dsn);
$database = $client->selectDatabase($dbname);

$cache = new MongoCache($database);
$cache->setOption('ttl', 3600);

```

``` php
<?php

use Desarrolla2\Cache\Mongo as MongoCache;

$client = new MongoClient($dsn);
$database = $client->selectDatabase($dbName);
$collection = $database->selectCollection($collectionName);

$cache = new MongoCache($collection);
$cache->setOption('ttl', 3600);

```

_Note that expired cache items aren't automatically deleted. To keep your
database clean, you should create a
[ttl index](https://docs.mongodb.org/manual/core/index-ttl/)._


```
db.items.createIndex( { "ttl": 1 }, { expireAfterSeconds: 30 } )
```

### Mysqli

Use it if you will you have mysqlnd available in your system.

``` php
<?php

use Desarrolla2\Cache\Mysqli as MysqliCache;

$cache = new MysqliCache();
$cache->setOption('ttl', 3600);

```


``` php
<?php
    
use Desarrolla2\Cache\Mysqli as MysqliCache;

$backend = new mysqli($dsn);
$cache = new MysqliCache($backend);
```

_Note that expired cache items aren't automatically deleted. To keep your
database clean, you should create a
[scheduled event](https://dev.mysql.com/doc/refman/5.7/en/event-scheduler.html)._

```
CREATE EVENT `clean_cache` ON SCHEDULE 1 DAY 
DO BEGIN
    DELETE FROM `cache
END;
```

### NotCache

Use it if you will not implement any cache adapter is an adapter that will
serve to fool the test environments.

``` php
<?php

use Desarrolla2\Cache\NotCache;

$cache = new NotCache();

```

### Predis

Use it if you will you have redis available in your system.

You need to add predis as dependency in your composer file.

``` json
"require": {
    //...
    "predis/predis": "~1.0.0"
}
```

other version will have compatibility issues.

``` php
<?php

use Desarrolla2\Cache\Predis as PredisCache;

$cache = new PredisCache();
```

If you need to configure your predis client, you will instantiate it and pass
it to constructor.

``` php
<?php

use Desarrolla2\Cache\Predis as PredisCache;
use Predis\Client as PredisClient;

$backend = new PredisClient($options);
$cache = new PredisCache($backend);

```

## Methods

The `Desarrolla2\Cache\CacheInterface` extends `Psr\SimpleCache\CacheInterface`
and defines the following methods:

##### `get(string $key)`
Retrieve the value corresponding to a provided key

##### `has(string $key)`
Retrieve the if value corresponding to a provided key exist

##### `set(string $key, mixed $value [, int $ttl])`
Add a value to the cache under a unique key

##### `delete(string $key)`
Delete a value from the cache

##### `clear()`
Clear all cache

##### `getMultiple(array $keys)`
Obtains multiple cache items by their unique keys

##### `setMultiple(array $values [, int $ttl])`
Persists a set of key => value pairs in the cache

##### `deleteMultiple(array $keys)`
Deletes multiple cache items in a single operation

##### `setOption(string $key, string $value)`
Set option for Adapter _(Not in PSR-16)_

##### `getOption(string $key)`
Get an option for Adapter _(Not in PSR-16)_


## Packers

Cache objects typically hold a `Desarrolla2\Cache\Packer\PackerInterface`
object. By default, packing is done using `serialize` and `unserialize`.

Available packers are:

* `JsonPacker` using `json_encode` and `json_decode`
* `NopPacker` does no packing
* `SerializePacker` using `serialize` and `unserialize`
* `PhpPacker` uses `var_export` and `include`/`eval`

#### PSR-16 incompatible packers

The `JsonPacker` does not fully comply with PSR-16, as packing and
unpacking an object will probably not result in an object of the same class.

The `NopPacker` is intended when caching string data only (like HTML output) or
if the caching backend supports structured data. Using it when storing objects
will likely yield unexpected results.


## Contact

You can contact with me on [@desarrolla2](https://twitter.com/desarrolla2).

## Contributors

[![Daniel González](https://avatars1.githubusercontent.com/u/661529?v=3&s=80)](https://github.com/desarrolla2)
[![Arnold Daniels](https://avatars3.githubusercontent.com/u/100821?v=3&s=80)](https://github.com/jasny)

[ico-version]: https://img.shields.io/packagist/v/desarrolla2/Cache.svg?style=flat-square
[ico-pre-release]: https://img.shields.io/packagist/vpre/desarrolla2/Cache.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/desarrolla2/Cache/master.svg?style=flat-square
[ico-coveralls]: https://img.shields.io/coveralls/desarrolla2/Cache/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/desarrolla2/cache.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/5f139261-1ac1-4559-846a-723e09319a88.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/desarrolla2/cache.svg?style=flat-square
[ico-today-downloads]: https://img.shields.io/packagist/dd/desarrolla2/cache.svg?style=flat-square
[ico-gitter]: https://img.shields.io/badge/GITTER-JOIN%20CHAT%20%E2%86%92-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/desarrolla2/cache
[link-license]: http://hassankhan.mit-license.org
[link-travis]: https://travis-ci.org/desarrolla2/Cache
[link-coveralls]: https://coveralls.io/github/desarrolla2/Cache
[link-code-quality]: https://scrutinizer-ci.com/g/desarrolla2/cache
[link-sensiolabs]: https://insight.sensiolabs.com/projects/5f139261-1ac1-4559-846a-723e09319a88
[link-downloads]: https://packagist.org/packages/desarrolla2/cache
[link-gitter]: https://gitter.im/desarrolla2/Cache?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge
