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

namespace Comely\Framework\AppKernel\Config;

use Comely\Framework\Exception\ConfigException;

/**
 * Class ErrorHandler
 * @package Comely\Framework\AppKernel\Config
 */
class ErrorHandler extends AbstractConfigNode
{
    /** @var string */
    private $format;

    /**
     * ErrorHandler constructor.
     * @param array $handler
     * @throws ConfigException
     */
    public function __construct(array $handler)
    {
        // Format
        $format = $handler["format"] ?? null;
        if (!$format || !is_string($format)) {
            throw ConfigException::PropError('error_handler.format', 'Invalid format');
        }

        $this->format = $format;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return $this->format;
    }
}