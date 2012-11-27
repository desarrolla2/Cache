<?php

/**
 * This file is part of the Cache proyect.
 * 
 * Copyright (c)
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : common.php , UTF-8
 * @date : Nov 27, 2012 , 10:56:05 PM
 *
 */

use Desarrolla2\Timer\Timer;

$timer = new Timer();
for ($i = 1; $i <= 10000; $i++) {
    $cache->set(md5($i), md5($i), 3600);
}
$timer->mark('10.000 set');
for ($i = 1; $i <= 10000; $i++) {
    $cache->has(md5($i));
}
$timer->mark('10.000 has');
for ($i = 1; $i <= 10000; $i++) {
    $cache->get(md5($i));
}
$timer->mark('10.000 get');
var_dump($timer->get());