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
    public function getPrefix(): string;

    /**
     * Get the key with prefix
     *
     * @param string|mixed $key
     * @return string
     */
    public function make($key): string;
}