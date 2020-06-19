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
use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * Pack value through serialization
 */
class JsonPacker implements PackerInterface
{
    /**
     * Get cache type (might be used as file extension)
     *
     * @return string
     */
    public function getType()
    {
        return 'json';
    }

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
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function unpack($packed)
    {
        if (!is_string($packed)) {
            throw new InvalidArgumentException("packed value should be a string");
        }

        $ret = json_decode($packed);

        if (!isset($ret) && json_last_error()) {
            throw new \UnexpectedValueException("packed value is not a valid JSON string");
        }

        return $ret;
    }
}
