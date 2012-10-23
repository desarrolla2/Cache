<?php

/**
 * This file is part of the Cache proyect.
 * 
 * Copyright (c)
 * Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Desarrolla2\Cache\Adapter;

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Exception\MySQLCacheException;

/**
 * 
 * Description of MySQL
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es> 
 * @file : MySQL.php , UTF-8
 * @date : Oct 24, 2012 , 12:12:59 AM
 */
class MySQL extends AbstractAdapter implements AdapterInterface
{

    public function __construct()
    {
        throw new MySQLCacheException('this adapter is not ready yet');
    }

    public function delete($key)
    {
        
    }

    public function get($key)
    {
        
    }

    public function has($key)
    {
        
    }

    public function set($key, $value, $ttl = null)
    {
        
    }

    public function setOption($key, $value)
    {
        
    }

}
