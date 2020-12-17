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

namespace Desarrolla2\Cache\Packer;

/**
 * Support packing for Caching adapter
 */
trait PackingTrait
{
    /**
     * @var PackerInterface
     */
    protected $packer;


    /**
     * Create the default packer for this cache implementation
     *
     * @return PackerInterface
     */
    abstract protected static function createDefaultPacker(): PackerInterface;

    /**
     * Set a packer to pack (serialialize) and unpack (unserialize) the data.
     *
     * @param PackerInterface $packer
     * @return static
     */
    public function withPacker(PackerInterface $packer)
    {
        $cache = $this->cloneSelf();
        $cache->packer = $packer;

        return $cache;
    }

    /**
     * Get the packer
     *
     * @return PackerInterface
     */
    protected function getPacker(): PackerInterface
    {
        if (!isset($this->packer)) {
            $this->packer = static::createDefaultPacker();
        }

        return $this->packer;
    }

    /**
     * Pack the value
     *
     * @param mixed $value
     * @return string|mixed
     */
    protected function pack($value)
    {
        return $this->getPacker()->pack($value);
    }

    /**
     * Unpack the data to retrieve the value
     *
     * @param string|mixed $packed
     * @return mixed
     * @throws \UnexpectedValueException
     */
    protected function unpack($packed)
    {
        return $this->getPacker()->unpack($packed);
    }
}
