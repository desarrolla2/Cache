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
use Desarrolla2\Cache\Exception\BadMethodCallException;

/**
 * Export to code that can be evaluated by PHP.
 *
 * This packer only works for file caching.
 * It can be expected to behave much faster to normal caching, as opcode cache is utilized.
 */
class PhpPacker implements PackerInterface
{
    /**
     * Get cache type (might be used as file extension)
     *
     * @return string
     */
    public function getType()
    {
        return 'php';
    }

    /**
     * Pack the value
     * 
     * @param mixed $value
     * @return string
     */
    public function pack($value)
    {
        $macro = var_export($value, true);

        if (strpos($macro, 'stdClass::__set_state') !== false) {
            $macro = preg_replace_callback("/('([^'\\\\]++|''\\.)')|stdClass::__set_state/", $macro, function($match) {
                return empty($match[1]) ? '(object)' : $match[1];
            });
        }

        return '<?php return ' . $macro . ';';
    }
    
    /**
     * Unpack the value
     * 
     * @param string $packed
     * @throws BadMethodCallException
     */
    public function unpack($packed)
    {
        BadMethodCallException("PHP packer should not be used in combination with file cache");
    }
}
