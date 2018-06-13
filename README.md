# Cache [<img alt="SensioLabsInsight" src="https://insight.sensiolabs.com/projects/5f139261-1ac1-4559-846a-723e09319a88/small.png" align="right">](https://insight.sensiolabs.com/projects/5f139261-1ac1-4559-846a-723e09319a88)

A simple cache library, implementing the [PSR-16](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md) standard using **immutable** objects.

Caching is typically used throughout an applicatiton. Immutability ensure that modifying the cache behaviour in one
location doesn't result in unexpect behaviour in unrelated code.

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
use Desarrolla2\Cache\Memory as Cache;

$cache = new Cache();

$cache->set('key', 'myKeyValue', 3600);

// later ...

echo $cache->get('key');
```

### Options

You can set options for cache using the `withOption` or `withOptions` method.
Note that all cache objects are immutable, setting an option creates a new
object.

#### TTL

All cache implementations support the `ttl` option. This sets the default
time (in seconds) that cache will survive. It defaults to one hour (3600
seconds).

Setting the TTL to 0 or a negative number, means the cache should live forever.

## Cache implementations

* [Apcu](#apcu)
* [File](#file)
* [Memcached](#memcached)
* [Memory](#memory)
* [Mongo](#mongo)
* [Mysqli](#mysqli)
* [NotCache](#notcache)
* [PhpFile](#phpfile)
* [Predis](#predis)

### Apcu

Use [APCu cache](http://php.net/manual/en/book.apcu.php) to cache to shared
memory.

``` php
use Desarrolla2\Cache\Apcu as ApcuCache;

$cache = new ApcuCache();
```

_Note: by default APCu uses the time at the beginning of a request for ttl. In
some cases, like with a long running script, this can be a problem. You can
change this behaviour `ini_set('apc.use_request_time', false)`._

### File

Save the cache as file to on the filesystem.

The file contains the TTL as well
as the data.

``` php
use Desarrolla2\Cache\CacheFile as CacheFileCache;

$cache = new CacheFileCache();
```

You may set the following options;

``` php
use Desarrolla2\Cache\CacheFile as CacheFileCache;

$cache = (new CacheFileCache())->withOptions([
    'dir' => '/tmp/mycache',
    'file-prefix' => '',
    'file-suffix' => '.php.cache',
    'ttl' => 3600
]);
```

### Memcached

Store cache to [Memcached](https://memcached.org/). Memcached is a high
performance distributed caching system.

``` php
use Desarrolla2\Cache\Memcached as MemcacheCache;

$cache = new MemcacheCache();
```

You can config your connection before

``` php
use Desarrolla2\Cache\Memcached as MemcachedCache;
use Memcached;

$server = new Memcached();
// configure it here

$cache = new MemcachedCache($server);
```

This implementation uses the [memcached](https://php.net/memcached) php
extension. The (alternative) memcache extension is not supported.

### Memory

Store the cache in process memory. Cache Memory is removed when the PHP process
exist. Also it is not shared between different processes.

Memory cache have a option `limit`, that limit the max items in cache.

``` php
use Desarrolla2\Cache\Memory as MemoryCache;

$cache = new MemoryCache();
```

You may set the following options;

``` php
use Desarrolla2\Cache\Memory as MemoryCache;

$cache = (new MemoryCache())->withOptions([
    'ttl' => 3600,
    'limit' => 200
]);
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

### PhpFile

Save the cache as PHP script to on the filesystem using `var_export` when
storing the cache and `include` when loading the cache. This method is
particularly fast in PHP7.2+ due to opcache optimizations.

``` php
use Desarrolla2\Cache\PhpFile as PhpFileCache;

$cache = new FileCache();
```

You may set the following options;

``` php
use Desarrolla2\Cache\CacheFile as CacheFileCache;

$cache = (new FileCache())->withOptions([
    'dir' => '/tmp/mycache',
    'file-prefix' => '',
    'ttl' => 3600
]);
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

Each cache implementation has the following `Psr\SimpleCache\CacheInterface`
methods:

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

The `Desarrolla2\Cache\CacheInterface` also has the following methods:

##### `withOption(string $key, string $value)`
Set option for implementation. Creates a new instance.

##### `withOptions(array $options)`
Set multiple options for implementation. Creates a new instance.

##### `getOption(string $key)`
Get option for implementation.


## Packers

Cache objects typically hold a `Desarrolla2\Cache\Packer\PackerInterface`
object. By default, packing is done using `serialize` and `unserialize`.

Available packers are:

* `JsonPacker` using `json_encode` and `json_decode`
* `NopPacker` does no packing
* `SerializePacker` using `serialize` and `unserialize`

#### PSR-16 incompatible packers

The `JsonPacker` does not fully comply with PSR-16, as packing and
unpacking an object will probably not result in an object of the same class.

The `NopPacker` is intended when caching string data only (like HTML output) or
if the caching backend supports structured data. Using it when storing objects
will might give unexpected results.

## Contributors

[![Daniel González](https://avatars1.githubusercontent.com/u/661529?v=3&s=80)](https://github.com/desarrolla2)
Twitter: [@desarrolla2](https://twitter.com/desarrolla2)\
[![Arnold Daniels](https://avatars3.githubusercontent.com/u/100821?v=3&s=80)](https://github.com/jasny)
Twitter: [@ArnoldDaniels](https://twitter.com/ArnoldDaniels)

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
