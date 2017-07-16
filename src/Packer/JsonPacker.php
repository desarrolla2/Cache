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
 * @author Arnold Daniels <arnold@jasny.net>
 */

namespace Desarrolla2\Cache\Packer;

use Desarrolla2\Cache\Packer\PackerInterface;

/**
 * Pack value through serialization
 */
class JsonPacker implements PackerInterface
{
    /**
     * Pack the value
     * 
     * @param mixed $value
     * @return string
     */
    public function pack($value)
    {
        return json_encode($value);
    }
    
    /**
     * Unpack the value
     * 
     * @param string $packed
     * @return string
     * @throws \UnexpectedValueException if he 
     */
    public function unpack($packed)
    {
        $value = json_decode($packed);
        
        return json_serialize($packed);
    }
}
