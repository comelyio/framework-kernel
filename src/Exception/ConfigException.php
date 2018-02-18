<?php
/**
 * This file is part of Comely Framework Kernel package.
 * https://github.com/comelyio/framework-kernel
 *
 * Copyright (c) 2016-2018 Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/comelyio/framework-kernel/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Comely\Framework\Exception;

/**
 * Class ConfigException
 * @package Comely\Framework\Exception
 */
class ConfigException extends BootstrapException
{
    /**
     * @param string $prop
     * @param string $message
     * @return ConfigException
     */
    public static function PropError(string $prop, string $message): self
    {
        return new self(sprintf('Config. [%s] error: %s', $prop, $message));
    }

    /**
     * @param string $tag
     * @param string $message
     * @return ConfigException
     */
    public static function DatabaseError(string $tag, string $message): self
    {
        return new self(sprintf('Database [%s] error: %s', $tag, $message));
    }
}