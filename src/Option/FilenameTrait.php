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

use TypeError;
use Desarrolla2\Cache\File\BasicFilename;

/**
 * Use filename generator
 */
trait FilenameTrait
{
    /**
     * @var callable
     */
    protected $filename;


    /**
     * Filename format or callable.
     * The filename format will be applied using sprintf, replacing `%s` with the key.
     *
     * @param string|callable $filename
     * @return void
     */
    protected function setFilenameOption($filename): void
    {
        if (is_string($filename)) {
            $filename = new BasicFilename($filename);
        }

        if (!is_callable($filename)) {
            throw new TypeError("Filename should be a string or callable");
        }

        $this->filename = $filename;
    }

    /**
     * Get the filename callable
     *
     * @return callable
     */
    protected function getFilenameOption(): callable
    {
        if (!isset($this->filename)) {
            $this->filename = new BasicFilename('%s.' . $this->getPacker()->getType());
        }

        return $this->filename;
    }

    /**
     * Create a filename based on the key
     *
     * @param string|mixed $key
     * @return string
     */
    protected function getFilename($key): string
    {
        $id = $this->keyToId($key);
        $generator = $this->getFilenameOption();

        return $this->cacheDir . DIRECTORY_SEPARATOR . $generator($id);
    }

    /**
     * Get a wildcard for all files
     *
     * @return string
     */
    protected function getWildcard(): string
    {
        $generator = $this->getFilenameOption();

        return $this->cacheDir . DIRECTORY_SEPARATOR . $generator('');
    }
}
