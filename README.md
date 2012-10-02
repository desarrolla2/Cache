# Cache

A independent cache library.

build status : [![Build Status](https://secure.travis-ci.org/desarrolla2/Cache.png)](http://travis-ci.org/desarrolla2/Cache)

## Installation

It is best installed it through [packagist](http://packagist.org/packages/desarrolla2/cache) by including
`desarrolla2/cache` in your project composer.json require:

``` json
    "require": {
        "desarrolla2/cache":  "dev-master"
    }
```

You can also download it from [Github] (https://github.com/desarrolla2/Cache), but no autoloader is provided so you'll need to register it with your own PSR-0 compatible autoloader.

## Usage


``` php

    use Desarrolla2\Cache\Cache;
    use Desarrolla2\Cache\Adapter\Apc;

    $cache = new Cache(new Apc());

    $cache->set('key', 'myKeyValue', 3600);

    // later ...

    echo $cache->get('key');

```

## Coming soon

* FileAdapter
* MemcachedAdapter
* More test

## Contact

You can contact with me on [twitter](https://twitter.com/desarrolla2).