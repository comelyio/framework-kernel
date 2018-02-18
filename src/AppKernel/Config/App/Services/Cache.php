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
 * Class Cache
 * @package Comely\Framework\AppKernel\Config\App\Services
 * @method string engine()
 * @method string host()
 * @method int port()
 * @method bool terminate()
 */
class Cache extends AbstractConfigNode
{
    /** @var string */
    private $engine;
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var bool */
    private $terminate;

    /**
     * Cache constructor.
     * @param array $options
     * @throws ConfigException
     */
    public function __construct(array $options)
    {
        // Engine
        $engine = $options["engine"] ?? null;
        if (!is_string($engine)) {
            throw ConfigException::PropError('app.services.cache', 'Property "engine" must be of type string');
        }

        $this->engine = $engine;

        // Host
        $host = $options["host"] ?? null;
        if (!is_string($host)) {
            throw ConfigException::PropError('app.services.cache', 'Property "host" must be of type string');
        }

        $this->host = $host;

        // Port
        $port = $options["port"] ?? null;
        if (!is_int($port)) {
            throw ConfigException::PropError('app.services.cache', 'Property "port" must be of type integer');
        }

        $this->port = $port;

        // Terminate?
        $terminate = $options["terminate"] ?? null;
        if (!is_bool($terminate)) {
            throw ConfigException::PropError('app.services.cache', 'Property "terminate" must be "true" or "false"');
        }

        $this->terminate = $terminate;
    }
}