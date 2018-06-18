<?php
/**
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

namespace Desarrolla2\Cache\Option;

/**
 * Prefix option
 */
trait PrefixTrait
{
    /**
     * @var string
     */
    protected $prefix = '';


    /**
     * Set the key prefix
     *
     * @param string $prefix
     * @return void
     */
    protected function setPrefixOption(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * Get the key prefix
     *
     * @return string
     */
    protected function getPrefixOption(): string
    {
        return $this->prefix;
    }
}
