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

namespace Desarrolla2\Test\Cache;

use Desarrolla2\Cache\File as FileCache;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * FileTest
 */
class FileTtlFileTest extends AbstractCacheTest
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    protected $skippedTests = [
        'testBasicUsageWithLongKey' => 'Only support keys up to 64 bytes'
    ];

    public function createSimpleCache()
    {
        $this->root = vfsStream::setup('cache');

        return (new FileCache(vfsStream::url('cache')))
            ->withOption('ttl-strategy', 'file');
    }
}
