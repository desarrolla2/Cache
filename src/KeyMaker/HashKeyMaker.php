<?php

namespace Desarrolla2\Cache\KeyMaker;

use Desarrolla2\Cache\KeyMaker\AbstractKeyMaker;
use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * Generates a key for storage by hashing it
 */
class HashKeyMaker extends AbstractKeyMaker
{
    /**
     * @var string
     */
    protected $algo;


    /**
     * class constructor
     *
     * @param string $algo
     * @param string $prefix
     * @param bool   $psr16  PSR-16 key validation
     */
    public function __construct(string $algo = 'sha1', string $prefix = '')
    {
        $this->algo = $algo;
        $this->prefix = $prefix;
    }

    /**
     * Lenient key validation
     *
     * @param string|mixed $key
     * @throws InvalidArgumentException
     */
    protected function validateKey($key)
    {
        if (!is_scalar($key)) {
            $type = (is_object($key) ? get_class($key) . ' ' : '') . gettype($key);
            throw new InvalidArgumentException("Expected key to be a scalar, not $type");
        }
    }

    /**
     * Get the key with prefix
     *
     * @param string|mixed $key
     * @return string
     */
    public function make($key): string
    {
        $this->validateKey($key);

        return hash($this->algo, sprintf('%s%s', $this->prefix, $key));
    }
}
