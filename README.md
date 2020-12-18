# Desarolla2 Cache

A **simple cache** library, implementing the [PSR-16](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md) standard using **immutable** objects.

![life-is-hard-cache-is](https://user-images.githubusercontent.com/100821/41566888-ecd60cde-735d-11e8-893f-da42b2cd65e7.jpg)

Caching is typically used throughout an applicatiton. Immutability ensure that modifying the cache behaviour in one
location doesn't result in unexpected behaviour due to changes in unrelated code.

_Desarolla2 Cache aims to be the most complete, correct and best performing PSR-16 implementation available._

[![Latest version][ico-version]][link-packagist]
[![Latest version][ico-pre-release]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-github-actions]][link-github-actions]
[![Coverage Status][ico-coverage]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-scrutinizer]
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

$value = $cache->get('key');

if (!isset($value)) {
    $value = do_something(); 
    $cache->set('key', $value, 3600);
}

echo $value;
```

## Adapters

* [Apcu](docs/implementations/apcu.md)
* [File](docs/implementations/file.md)
* [Memcached](docs/implementations/memcached.md)
* [Memory](docs/implementations/memory.md)
* [MongoDB](docs/implementations/mongodb.md)
* [Mysqli](docs/implementations/mysqli.md)
* [NotCache](docs/implementations/notcache.md)
* [PhpFile](docs/implementations/phpfile.md)
* [Predis](docs/implementations/predis.md)

The following implementation allows you to combine cache adapters.

* [Chain](docs/implementations/chain.md)

[Other implementations][todo-implementations] are planned. Please vote or
provide a PR to speed up the process of adding the to this library.

[todo-implementations]: https://github.com/desarrolla2/Cache/issues?q=is%3Aissue+is%3Aopen+label%3Aadapter

### Options

You can set options for cache using the `withOption` or `withOptions` method.
Note that all cache objects are immutable, setting an option creates a new
object.

#### TTL

All cache implementations support the `ttl` option. This sets the default
time (in seconds) that cache will survive. It defaults to one hour (3600
seconds).

Setting the TTL to 0 or a negative number, means the cache should live forever.

## Methods

Each cache implementation has the following `Psr\SimpleCache\CacheInterface`
methods:

##### `get(string $key [, mixed $default])`
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

.

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

* `SerializePacker` using `serialize` and `unserialize`
* `JsonPacker` using `json_encode` and `json_decode`
* `NopPacker` does no packing
* `MongoDBBinaryPacker` using `serialize` and `unserialize` to store as [BSON Binary](http://php.net/manual/en/class.mongodb-bson-binary.php)

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
[ico-coverage]: https://scrutinizer-ci.com/g/desarrolla2/Cache/badges/coverage.png?b=master
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/5f139261-1ac1-4559-846a-723e09319a88.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/desarrolla2/cache.svg?style=flat-square
[ico-today-downloads]: https://img.shields.io/packagist/dd/desarrolla2/cache.svg?style=flat-square
[ico-gitter]: https://img.shields.io/badge/GITTER-JOIN%20CHAT%20%E2%86%92-brightgreen.svg?style=flat-square
[ico-github-actions]: https://github.com/desarrolla2/Cache/workflows/PHP/badge.svg

[link-packagist]: https://packagist.org/packages/desarrolla2/cache
[link-license]: http://hassankhan.mit-license.org
[link-travis]: https://travis-ci.org/desarrolla2/Cache
[link-github-actions]: https://github.com/desarrolla2/Cache/actions
[link-coveralls]: https://coveralls.io/github/desarrolla2/Cache
[link-scrutinizer]: https://scrutinizer-ci.com/g/desarrolla2/cache
[link-sensiolabs]: https://insight.sensiolabs.com/projects/5f139261-1ac1-4559-846a-723e09319a88
[link-downloads]: https://packagist.org/packages/desarrolla2/cache
[link-gitter]: https://gitter.im/desarrolla2/Cache?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge
