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

namespace Desarrolla2\Cache;

use Desarrolla2\Cache\AbstractFile;
use Desarrolla2\Cache\Packer\PackerInterface;
use Desarrolla2\Cache\Packer\SerializePacker;
use Desarrolla2\Cache\File\BasicFilename;

/**
 * Cache file as PHP script.
 */
class PhpFile extends AbstractFile
{
    /**
     * Create the default packer for this cache implementation.
     *
     * @return PackerInterface
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new SerializePacker();
    }

    /**
     * Get the filename callable
     *
     * @return callable
     */
    protected function getFilenameOption(): callable
    {
        if (!isset($this->filename)) {
            $this->filename = new BasicFilename('%s.php');
        }

        return $this->filename;
    }

    /**
     * Create a PHP script returning the cached value
     *
     * @param mixed    $value
     * @param int|null $ttl
     * @return string
     */
    public function createScript($value, ?int $ttl): string
    {
        $macro = var_export($value, true);

        if (strpos($macro, 'stdClass::__set_state') !== false) {
            $macro = preg_replace_callback("/('([^'\\\\]++|''\\.)')|stdClass::__set_state/", $macro, function($match) {
                return empty($match[1]) ? '(object)' : $match[1];
            });
        }

        return $ttl !== null
            ? "<?php return time() < {$ttl} ? {$macro} : false;"
            : "<?php return {$macro};";
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $cacheFile = $this->getFilename($key);

        if (!file_exists($cacheFile)) {
            return $default;
        }

        $packed = include $cacheFile;

        return $packed === false ? $default : $this->unpack($packed);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->get($key) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $cacheFile = $this->getFilename($key);

        $packed = $this->pack($value);
        $script = $this->createScript($packed, $this->ttlToTimestamp($ttl));

        return $this->writeFile($cacheFile, $script);
    }
}
