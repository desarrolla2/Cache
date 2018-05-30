<?php

namespace Desarrolla2\Cache\KeyMaker;
use Desarrolla2\Cache\KeyMaker\KeyMakerInterface;

/**
 * Generates a key for storage by hashing it
 */
class HashKeyMaker implements KeyMakerInterface
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $algo;

    /**
     * class constructor
     *
     * @param string $prefix
     */
    public function __construct($algo = 'sha1', $prefix = '')
    {
        $this->prefix = $prefix;
        $this->algo = $algo;
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
        return hash($this->algo, sprintf('%s%s', $this->prefix, $key));
    }
}