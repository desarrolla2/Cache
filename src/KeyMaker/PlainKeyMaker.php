<?php

namespace Desarrolla2\Cache\KeyMaker;
use Desarrolla2\Cache\KeyMaker\KeyMakerInterface;

/**
 * Generates a key for storage
 */
class PlainKeyMaker implements KeyMakerInterface
{
    /**
     * @var string
     */
    protected $prefix;

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
     * Get the key prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Get the key with prefix
     *
     * @param string $key
     * @return string
     */
    public function make($key)
    {
        return sprintf('%s%s', $this->prefix, $key);
    }
}