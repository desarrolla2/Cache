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

/**
 * Interface for packer / unpacker
 */
interface PackerInterface
{
    /**
     * Get cache type (might be used as file extension)
     *
     * @return string
     */
    public function getType();

    /**
     * Pack the value
     * 
     * @param mixed $value
     * @return string|mixed
     */
    public function pack($value);
    
    /**
     * Unpack the value
     * 
     * @param string|mixed $packed
     * @return string
     * @throws \UnexpectedValueException if the value can't be unpacked
     */
    public function unpack($packed);
}
