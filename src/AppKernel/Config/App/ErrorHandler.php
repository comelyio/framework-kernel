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

namespace Comely\Framework\AppKernel\Config\App;

use Comely\Framework\AppKernel\Config\AbstractConfigNode;
use Comely\Framework\Exception\ConfigException;

/**
 * Class ErrorHandler
 * @package Comely\Framework\AppKernel\Config\App
 * @method string format()
 * @method array screen()
 */
class ErrorHandler extends AbstractConfigNode
{
    /** @var string */
    public $format;
    /** @var array */
    public $screen;

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
            throw ConfigException::PropError('app.error_handler.format', 'Invalid format');
        }

        // Screen
        $this->screen = [
            "debug_backtrace" => $handler["screen"]["debug_backtrace"] ?? null,
            "triggered_errors" => $handler["screen"]["triggered_errors"] ?? null,
            "complete_paths" => $handler["screen"]["complete_paths"] ?? null,
        ];

        foreach ($this->screen as $prop => $val) {
            if (!is_bool($val)) {
                throw ConfigException::PropError(
                    'app.error_handler.screen',
                    sprintf('Property "%s" must be of type boolean', $prop)
                );
            }
        }
    }
}