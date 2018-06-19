# NotCache

A [Null object](https://sourcemaking.com/design_patterns/null_object) that
correctly implements the PSR-16 interface, but does not actually cache
anything.

``` php
use Desarrolla2\Cache\NotCache;

$cache = new NotCache();
```

It doesn't use any options or packers.
