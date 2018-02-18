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

namespace Comely\Framework\AppKernel\Config\App\Services;

use Comely\Framework\AppKernel\Config\AbstractConfigNode;
use Comely\Framework\Exception\ConfigException;

/**
 * Class Translator
 * @package Comely\Framework\AppKernel\Config\App\Services
 * @method string fallBack()
 * @method bool caching()
 */
class Translator extends AbstractConfigNode
{
    /** @var string */
    private $fallBack;
    /** @var bool */
    private $caching;

    /**
     * Translator constructor.
     * @param array $opts
     * @throws ConfigException
     */
    public function __construct(array $opts)
    {
        // Default & Fallback language
        $fallBack = $opts["fall_back"] ?? $opts["fallBack"] ?? null;
        if (!is_string($fallBack)) {
            throw ConfigException::PropError(
                'app.services.translator',
                sprintf('Property "fall_back" must be of type string')
            );
        }

        $this->fallBack = $fallBack;

        // Caching
        $caching = $opts["caching"] ?? $opts["cache"] ?? null;
        if (!is_bool($caching)) {
            throw ConfigException::PropError(
                'app.services.translator',
                sprintf('Property "caching" can be either of "on" or "off"')
            );
        }

        $this->caching = $caching;
    }
}