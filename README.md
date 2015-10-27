# Cache

A simple cache library. Implements different adapters that you can use and change 
easily by a manager or similar.


[![Latest version][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-coveralls]][link-coveralls]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]
[![Gitter][ico-gitter]][link-gitter]

## Installation

### With Composer

It is best installed it through [packagist](http://packagist.org/packages/desarrolla2/cache) 
by including `desarrolla2/cache` in your project composer.json require:

``` json
    "require": {
        // ...
        "desarrolla2/cache":  "~2.0"
    }
```

### Without Composer

You can also download it from [Github] (https://github.com/desarrolla2/Cache), 
but no autoloader is provided so you'll need to register it with your own PSR-0 
compatible autoloader.

## Usage


``` php
<?php

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\NotCache;

$cache = new Cache(new NotCache());

$cache->set('key', 'myKeyValue', 3600);

// later ...

echo $cache->get('key');

```

## Adapters

### Apcu

Use it if you will you have APC cache available in your system.

``` php
<?php
    
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Apcu;

$adapter = new Apcu();
$adapter->setOption('ttl', 3600);
$cache = new Cache($adapter);

```

### File

Use it if you will you have dont have other cache system available in your system
or if you like to do your code more portable.

``` php
<?php
    
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\File;

$cacheDir = '/tmp';
$adapter = new File($cacheDir);
$adapter->setOption('ttl', 3600);
$cache = new Cache($adapter);

```

### Memcache

Use it if you will you have mencache available in your system.

``` php
<?php
    
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Memcache;

$adapter = new Memcache();
$cache = new Cache($adapter);

```

You can config your connection before


``` php
<?php
    
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Memcache;    
use \Memcache as Backend

$backend = new Backend();
// configure it here

$cache = new Cache(new Memcache($backend));
```

### Memcached

Is the same like mencache adapter.

### Memory

This is the fastest cache type, since the elements are stored in memory. 
Cache Memory such is very volatile and is removed when the process terminates.
Also it is not shared between different processes.

Memory cache have a option "limit", that limit the max items in cache.

``` php
<?php
    
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Memory;

$adapter = new Memory();
$adapter->setOption('ttl', 3600);
$adapter->setOption('limit', 200);
$cache = new Cache($adapter);

```


### Mysqli

Use it if you will you have mysqlnd available in your system.

``` php
<?php

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Mysqli;

$adapter = new Mysqli();
$adapter->setOption('ttl', 3600);
$cache = new Cache($adapter);

```


``` php
<?php
    
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Mysqli;    
use \mysqli as Backend

$backend = new Backend();
// configure it here

$cache = new Cache(new Mysqli($backend));
```

### NotCache

Use it if you will not implement any cache adapter is an adapter that will serve 
to fool the test environments.

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

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Predis;

$adapter = new Predis();
$cache = new Cache($adapter);

```

If you need to configure your predis client, you will instantiate it and pass it to constructor.

``` php
<?php

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Predis;
use Predis\Client as Backend

$adapter = new Predis(new Backend($options));
$cache = new Cache($adapter);

```

## Coming soon

This library implements other adapters as soon as possible, feel free to send 
new adapters if you think it appropriate.

This can be a list of pending tasks.

* Cleaning cache
* MemcachedAdapter
* Other Adapters

## Contact

You can contact with me on [@desarrolla2](https://twitter.com/desarrolla2).

[ico-version]: https://img.shields.io/packagist/v/desarrolla2/cache.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/desarrolla2/Cache/master.svg?style=flat-square
[ico-coveralls]: https://img.shields.io/coveralls/desarrolla2/Cache/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/desarrolla2/cache.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/desarrolla2/cache.svg?style=flat-square
[ico-gitter]: https://img.shields.io/badge/GITTER-JOIN%20CHAT%20%E2%86%92-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/desarrolla2/cache
[link-license]: http://hassankhan.mit-license.org
[link-travis]: https://travis-ci.org/desarrolla2/Cache
[link-coveralls]: https://coveralls.io/github/desarrolla2/Cache
[link-code-quality]: https://scrutinizer-ci.com/g/desarrolla2/cache
[link-downloads]: https://packagist.org/packages/desarrolla2/cache
[link-gitter]: https://gitter.im/desarrolla2/Cache?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge
