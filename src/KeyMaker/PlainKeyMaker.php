<?php

namespace Desarrolla2\Cache\KeyMaker;
use Desarrolla2\Cache\Exception\InvalidArgumentException;
use Desarrolla2\Cache\KeyMaker\AbstractKeyMaker;

/**
 * Generates a key for storage
 */
class PlainKeyMaker extends AbstractKeyMaker
{
    /**
     * class constructor
     *
     * @param string $prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Get the key with prefix
     *
     * @param string|mixed $key
     * @return string
     * @throws InvalidArgumentException  if key is not a alphanumeric string
     */
    public function make($key): string
    {
        $this->validateKey($key);

        return sprintf('%s%s', $this->prefix, $key);
    }
}
