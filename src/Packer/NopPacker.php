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

declare(strict_types=1);

namespace Desarrolla2\Cache\Packer;

use Desarrolla2\Cache\Packer\PackerInterface;

/**
 * Don't pack, just straight passthrough
 */
class NopPacker implements PackerInterface
{
    /**
     * Get cache type (might be used as file extension)
     *
     * @return string
     */
    public function getType()
    {
        return 'data';
    }

    /**
     * Pack the value
     * 
     * @param mixed $value
     * @return mixed
     */
    public function pack($value)
    {
        return $value;
    }
    
    /**
     * Unpack the value
     * 
     * @param mixed $packed
     * @return mixed
     */
    public function unpack($packed)
    {
        return $packed;
    }
}
