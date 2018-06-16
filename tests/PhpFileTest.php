<?php

/*
 * This file is part of the Cache package.
 *
 * Copyright (c) Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@desarrolla2.com>
 */

namespace Desarrolla2\Test\Cache;

use Desarrolla2\Cache\FlatFile as FileCache;
use Desarrolla2\Cache\Packer\PhpPacker;
use Desarrolla2\Cache\PhpFile;

/**
 * FileTest with PhpPacker
 */
class PhpFileTest extends AbstractCacheTest
{
    protected $skippedTests = [
        'testBasicUsageWithLongKey' => 'Only support keys up to 64 bytes'
    ];


    public function createSimpleCache()
    {
        return new PhpFile($this->config['file']['dir']);
    }

    /**
     * Remove all temp dir with cache files
     */
    public function tearDown() 
    {
        array_map('unlink', glob($this->config['file']['dir'] . "/*"));
        rmdir($this->config['file']['dir']);
    }
}
