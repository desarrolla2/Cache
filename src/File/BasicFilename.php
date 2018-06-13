<?php

namespace Desarrolla2\Cache\File;

/**
 * Create a path for a key
 */
class BasicFilename
{
    /**
     * @var string
     */
    protected $format;

    /**
     * BasicFilename constructor.
     *
     * @param string $format
     */
    public function __construct(string $format)
    {
        $this->format = $format;
    }

    /**
     * Get the format
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Create the path for a key
     *
     * @param string $key
     * @return string
     */
    public function __invoke(string $key): string
    {
        return sprintf($this->format, $key ?: '*');
    }

    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFormat();
    }
}