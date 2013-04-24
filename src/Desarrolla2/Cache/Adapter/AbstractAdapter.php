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

/**
 *
 * Description of AbstractAdapter
 *
 * @author : Daniel González <daniel.gonzalez@freelancemadrid.es>
 * @file : AbstractAdapter.php , UTF-8
 * @date : Oct 24, 2012 , 12:05:12 AM
 */
abstract class AbstractAdapter
{

    /**
     * @var int
     */
    protected $ttl = 3600;

    /**
     * {@inheritdoc }
     */
    public function __construct()
    {
        
    }

    /**
     * {@inheritdoc }
     */
    public function clearCache()
    {
        throw new Exception('not ready yet');
    }

    /**
     * {@inheritdoc }
     */
    public function dropCache()
    {
        throw new Exception('not ready yet');
    }

}
