# Cache

[![Join the chat at https://gitter.im/desarrolla2/Cache](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/desarrolla2/Cache?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

A simple cache library. Implements different adapters that you can use and change 
easily by a manager or similar.

[![Build Status](https://secure.travis-ci.org/desarrolla2/Cache.png)](http://travis-ci.org/desarrolla2/Cache) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/desarrolla2/Cache/badges/quality-score.png?s=940939c8d0bf2056188455594f4332a002a968c2)](https://scrutinizer-ci.com/g/desarrolla2/Cache/) [![Code Coverage](https://scrutinizer-ci.com/g/desarrolla2/Cache/badges/coverage.png?s=16037142f461dcfdfd6ad57561e231881252197b)](https://scrutinizer-ci.com/g/desarrolla2/Cache/)

[![Latest Stable Version](https://poser.pugx.org/desarrolla2/cache/v/stable.png)](https://packagist.org/packages/desarrolla2/cache) [![Total Downloads](https://poser.pugx.org/desarrolla2/cache/downloads.png)](https://packagist.org/packages/desarrolla2/cache)



## Installation

### With Composer

It is best installed it through [packagist](http://packagist.org/packages/desarrolla2/cache) 
by including `desarrolla2/cache` in your project composer.json require:

``` json
    "require": {
        // ...
        "desarrolla2/cache":  "dev-master"
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

### NotCache

Use it if you will not implement any cache adapter is an adapter that will serve 
to fool the test environments.

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

### Mongo

Use it if you will you have mongodb available in your system.

``` php
<?php
    
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Mongo;

$server = 'mongodb://localhost:27017';
$adapter = new Mongo($server);
$adapter->setOption('ttl', 3600);
$cache = new Cache($adapter);

```

### Mysqli

Use it if you will you have mysqlnd available in your system.

``` php
<?php

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Mysqli;

$adapter = new Mysqli('localhost', 'user', 'pass', 'port');
$adapter->setOption('ttl', 3600);
$cache = new Cache($adapter);

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

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Predis;

$adapter = new Predis();
$adapter->setOption('ttl', 3600);
$cache = new Cache($adapter);

```

If you need to configure your predis client, you will instantiate it and pass it to constructor.

``` php
<?php

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Predis;
use Predis\Client;

$adapter = new Predis(new Client($options));
$cache = new Cache($adapter);

```

### Memcache

Use it if you will you have memcache available in your system.

``` php
<?php

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\Memcache;

$adapter = new Memcache();
$adapter->setOption('ttl', 3600);
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