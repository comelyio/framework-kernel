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

use Comely\Framework\AppKernel\Config\App\ErrorHandler;
use Comely\Framework\AppKernel\Config\App\Services;
use Comely\Framework\Exception\ConfigException;

/**
 * Class App
 * @package Comely\Framework\AppKernel\Config
 * @method string timeZone()
 * @method ErrorHandler errorHandler()
 * @method Services services()
 */
class App extends AbstractConfigNode
{
    /** @var string */
    private $timeZone;
    /** @var ErrorHandler */
    private $errorHandler;
    /** @var Services */
    private $services;

    /**
     * App constructor.
     * @param array $app
     * @throws ConfigException
     */
    public function __construct(array $app)
    {
        // Timezone
        $this->timeZone = $app["time_zone"] ?? $app["timeZone"] ?? null;
        if (!$this->timeZone || !is_string($this->timeZone)) {
            throw ConfigException::PropError('app.time_zone', 'Enter a valid timezone (i.e. "Europe/London")');
        }

        // Error handling
        $errorHandler = $app["error_handler"] ?? $app["errorHandler"] ?? null;
        if (!is_array($errorHandler)) {
            throw ConfigException::PropError('app.error_handler', 'Node must contain error handling specifications');
        }

        $this->errorHandler = new ErrorHandler($errorHandler);

        // Services
        $services = $app["services"] ?? null;
        if (!is_array($services)) {
            throw ConfigException::PropError('app.services', 'Node must contain app services');
        }

        $this->services = new Services($services);
    }
}