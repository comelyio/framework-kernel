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

namespace Comely\Framework;

use Comely\Framework\AppKernel\Config;
use Comely\Framework\AppKernel\DateTime;
use Comely\Framework\AppKernel\Directories;
use Comely\Framework\Exception\BootstrapException;
use Comely\IO\FileSystem\Disk\Directory;
use Comely\IO\FileSystem\Exception\DiskException;

/**
 * Class AppKernel
 * @package Comely\Framework
 */
class AppKernel
{
    protected const DIR_CONFIG = "app/config";
    protected const DIR_CACHE = "tmp/cache";
    protected const DIR_COMPILER = "tmp/compiler";
    protected const DIR_LANGS = "app/langs";
    protected const DIR_LOGS = "tmp/logs";

    public const MODE_DEV = 1101;
    public const MODE_PROD = 1102;

    /** @var Config */
    private $config;
    /** @var DateTime */
    private $dateTime;
    /** @var Directories */
    private $directories;

    private $errorHandler;
    private $memory;
    private $services;

    /**
     * AppKernel constructor.
     * @param array $options
     * @param string $env
     */
    public function __construct(array $options, string $env)
    {
        // Server type
        $serverType = $options["server"] ?? null;
        if (!$serverType || !in_array($serverType, ["web", "cli"])) {
            throw new BootstrapException('Invalid or no value for "server" option');
        }

        // Root directory
        $rootPath = $options["rootPath"] ?? $options["root_path"] ?? null;
        try {
            $rootDirectory = new Directory(strval($rootPath), null, true);
        } catch (DiskException $e) {
            if (!$rootPath) {
                throw new BootstrapException('Invalid or no value for "rootPath" option');
            }
            throw new BootstrapException('App root directory could not be located');
        }

        $this->directories = new Directories($this, $rootDirectory);

        // Configuration
        $loadCachedConfig = $options["loadCachedConfig"] ?? $options["load_cached_config"] ?? null;
        if (!is_bool($loadCachedConfig)) {
            throw new BootstrapException('Invalid value for "loadCachedConfig" option');
        }

        $this->config = new Config($this, $env);


        $this->dateTime = new DateTime();
    }

    /**
     * @param string $const
     * @return mixed
     */
    public function constant(string $const)
    {
        return @constant('static::' . $const);
    }

    /**
     * @return DateTime
     */
    public function dateTime(): DateTime
    {
        return $this->dateTime;
    }

    /**
     * @return Directories
     */
    public function directories(): Directories
    {
        return $this->directories;
    }


}