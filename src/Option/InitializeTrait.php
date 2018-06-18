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
 * Auto initialize the cache
 */
trait InitializeTrait
{
    /**
     * Is cache initialized
     * @var bool|null
     */
    protected $initialized = false;


    /**
     * Enable/disable initialization
     *
     * @param bool $enabled
     */
    public function setInitializeOption(bool $enabled)
    {
        $this->initialized = $enabled ? (bool)$this->initialized : null;
    }

    /**
     * Should initialize
     *
     * @return bool
     */
    protected function getInitializeOption(): bool
    {
        return $this->initialized !== null;
    }

    /**
     * Mark as initialization required (if enabled)
     */
    protected function requireInitialization()
    {
        $this->initialized = isset($this->initialized) ? false : null;
    }


    /**
     * Initialize
     *
     * @return void
     */
    abstract protected function initialize(): void;
}
