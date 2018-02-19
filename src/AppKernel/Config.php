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

namespace Comely\Framework\AppKernel;

use Comely\Framework\AppKernel;
use Comely\Framework\Exception\BootstrapException;
use Comely\Framework\Exception\ConfigException;
use Comely\IO\Yaml\Exception\YamlException;
use Comely\IO\Yaml\Yaml;


/**
 * Class Config
 * @package Comely\Framework\AppKernel
 * @method string env()
 * @method Config\Project project()
 * @method string timeZone()
 * @method Config\ErrorHandler errorHandler()
 * @method Config\Services services()
 */
class Config extends AppKernel\Config\AbstractConfigNode
{
    /** @var string */
    private $env;
    /** @var array */
    private $dbs;
    /** @var Config\Project */
    private $project;
    /** @var string */
    private $timeZone;
    /** @var Config\ErrorHandler */
    private $errorHandler;
    /** @var Config\Services */
    private $services;

    /**
     * Config constructor.
     * @param AppKernel $kernel
     * @param string $env
     */
    public function __construct(AppKernel $kernel, string $env)
    {
        // Check env. value
        if (!preg_match('/^[a-z]{2,16}$/', $env)) {
            throw new ConfigException('Invalid environment configuration name');
        }

        // Read YAML configuration
        try {
            $config = Yaml::Parse($kernel->directories()->config()->suffixed('env_' . $env . '.yml'))
                ->evaluateBooleans(false)
                ->generate();
        } catch (YamlException $e) {
            throw new BootstrapException(
                sprintf('Configure parse error: %s', $e->getMessage())
            );
        }

        // Environment
        $this->env = $env;

        // Timezone
        $this->timeZone = $config["time_zone"] ?? $config["timeZone"] ?? null;
        if (!$this->timeZone || !is_string($this->timeZone)) {
            throw ConfigException::PropError('time_zone', 'Enter a valid timezone (i.e. "Europe/London")');
        }

        // Error handling
        $errorHandler = $config["error_handler"] ?? $config["errorHandler"] ?? null;
        if (!is_array($errorHandler)) {
            throw ConfigException::PropError('error_handler', 'Node must contain error handling specifications');
        }

        $this->errorHandler = new AppKernel\Config\ErrorHandler($errorHandler);

        // Databases
        $this->dbs = [];
        $databases = $config["databases"] ?? false;
        if (is_array($databases)) {
            foreach ($databases as $tag => $db) {
                if (!is_string($tag) || !preg_match('/^[a-z\_]+$/', $tag)) {
                    throw ConfigException::PropError('databases', 'Contains an invalid database tag');
                }

                if (!is_array($db)) {
                    throw ConfigException::DatabaseError($tag, 'Node must contain database credentials');
                }

                $this->dbs[$tag] = new AppKernel\Config\Database($tag, $db);
            }
        } else {
            if (!is_null($databases)) {
                throw ConfigException::PropError('databases', 'Node must contain databases or NULL');
            }
        }

        // Project
        $project = $config["project"] ?? null;
        if (!is_array($project)) {
            throw ConfigException::PropError('project', 'Node must contain project specifications');
        }

        $this->project = new AppKernel\Config\Project($project);

        // Services
        $services = $config["services"] ?? null;
        if (!is_array($services)) {
            throw ConfigException::PropError('services', 'Node must contain app services');
        }

        $this->services = new AppKernel\Config\Services($services);
    }

    /**
     * @return array
     */
    public function databases(): array
    {
        return $this->dbs;
    }
}