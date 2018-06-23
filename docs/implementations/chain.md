# Chain

The Cache chain allows you to use multiple implementations to store cache. For
instance, you can use both fast volatile (in-memory) storage and slower
non-volatile (disk) storage. Alternatively you can have a local storage
as well as a shared storage service. 

``` php
use Desarrolla2\Cache\Chain as CacheChain;
use Desarrolla2\Cache\Memory as MemoryCache;
use Desarrolla2\Cache\Predis as PredisCache;

$cache = new CacheChain([
    (new MemoryCache())->withOption('ttl', 3600),
    (new PredisCache())->withOption('ttl', 10800)
]);
```

The Chain cache implementation doesn't use any option. It uses the `Nop` packer
by default.

Typically it's useful to specify a maximum `ttl` for each implementation. This
means that the volatile memory only holds items that are used often.

The following actions propogate to all cache adapters in the chain

* `set`
* `setMultiple`
* `delete`
* `deleteMultiple`
* `clear`

For the following actions all nodes are tried in sequence

* `has`
* `get`
* `getMultiple`