<?php

namespace Desarrolla2\Cache\KeyMaker;

/**
 * Interface for object that generates a key for storage
 */
interface KeyMakerInterface
{
    /**
     * Get the key prefix
     *
     * @return string
     */
    public function getPrefix();

    /**
     * Get the key with prefix
     *
     * @param string $key
     * @return string
     */
    public function make($key);
}