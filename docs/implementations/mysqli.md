# Mysqli

Cache to a [MySQL database](https://www.mysql.com/) using the
[mysqli](http://php.net/manual/en/book.mysqli.php) PHP extension.

You must pass a `mysqli` connection object to the constructor.

``` php
<?php
    
use Desarrolla2\Cache\Mysqli as MysqliCache;

$db = new mysqli('localhost');
$cache = new MysqliCache($db);
```

### Options

| name       | type      | default |                                       |
| ---------  | ----      | ------- | ------------------------------------- |
| initialize | bool      | true    | Enable auto-initialize                |
| ttl        | int       | null    | Maximum time to live in seconds       |
| prefix     | string    | ""      | Key prefix                            |

#### Initialize option

If `initialize` is enabled, the cache implementation will automatically create
a [scheduled event](https://dev.mysql.com/doc/refman/5.7/en/event-scheduler.html).

```
DELIMITER ;;

CREATE TABLE IF NOT EXISTS `cache` (`key` VARCHAR(255), `value` TEXT, `ttl` INT UNSIGNED, PRIMARY KEY (`key`));;

CREATE EVENT `apply_ttl_cache` ON SCHEDULE 1 HOUR 
DO BEGIN
    DELETE FROM `cache` WHERE `ttl` < NOW();
END;;
```

In production it's better to disable auto-initialization and create the event
explicitly when setting up the database. This prevents a `CREATE TABLE` and
`CREATE EVENT` query on each request. 

### Packer

By default the [`SerializePacker`](../packers/serialize.md) is used.
