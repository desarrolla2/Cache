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

declare(strict_types=1);

namespace Desarrolla2\Cache\Exception;

use Psr\SimpleCache\CacheException as PsrCacheException;

/**
 * Exception for unexpected values when reading from cache.
 */
class UnexpectedValueException extends \UnexpectedValueException implements PsrCacheException
{
}
