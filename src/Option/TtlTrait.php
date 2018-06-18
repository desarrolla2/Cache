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

use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * TTL option
 */
trait TtlTrait
{
    /**
     * @var int|null
     */
    protected $ttl = null;

    /**
     * Set the maximum time to live (ttl)
     *
     * @param int|null $value  Seconds or null to live forever
     * @throws InvalidArgumentException
     */
    protected function setTtlOption(?int $value): void
    {
        if (isset($value) && $value < 1) {
            throw new InvalidArgumentException('ttl cant be lower than 1');
        }

        $this->ttl = $value;
    }

    /**
     * Get the maximum time to live (ttl)
     *
     * @return int|null
     */
    protected function getTtlOption(): ?int
    {
        return $this->ttl;
    }
}