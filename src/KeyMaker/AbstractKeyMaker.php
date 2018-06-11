<?php
/**
 * Created by PhpStorm.
 * User: arnold
 * Date: 11-6-18
 * Time: 3:38
 */

namespace Desarrolla2\Cache\KeyMaker;

use Desarrolla2\Cache\Exception\InvalidArgumentException;

/**
 * Abstract base class for keymaker
 */
abstract class AbstractKeyMaker implements KeyMakerInterface
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * Get the key prefix
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }


    /**
     * PSR-16 key validation
     *
     * @param string|mixed $key
     * @throws InvalidArgumentException
     */
    protected function validateKey($key)
    {
        if (!is_string($key)) {
            $type = (is_object($key) ? get_class($key) . ' ' : '') . gettype($key);
            throw new InvalidArgumentException("Expected key to be a string, not $type");
        }

        if ($key === '' || preg_match('~[{}()/\\\\@:]~', $key)) {
            throw new InvalidArgumentException("Invalid key '$key'");
        }
    }
}