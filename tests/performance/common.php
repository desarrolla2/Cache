<?php

/*
 * This file is part of the Cache package.
 *
 * Copyright (c) Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@desarrolla2.com>
 */

use Desarrolla2\Timer\Timer;

//build test data outside of timing loop
$data=array();
for ($i = 1; $i <= 10000; $i++) {
    $data[$i]=md5($i);
}

$timer = new Timer();
for ($i = 1; $i <= 10000; $i++) {
    $cache->set($data[$i], $data[$i], 3600);
}
$timer->mark('10.000 set');
for ($i = 1; $i <= 10000; $i++) {
    $cache->has($data[$i]);
}
$timer->mark('10.000 has');
for ($i = 1; $i <= 10000; $i++) {
    $cache->get($data[$i]);
}
$timer->mark('10.000 get');
for ($i = 1; $i <= 10000; $i++) {
    $cache->has($data[$i]);
    $cache->get($data[$i]);
}
$timer->mark('10.000 has+get combos');

$benchmarks=$timer->get();
foreach ($benchmarks as $benchmark) {
    printf(
        "%30s : duration %0.2fms memory %s\n",
        $benchmark['text'],
        $benchmark['from_previous']*1000,
        $benchmark['memory']
    );
}
