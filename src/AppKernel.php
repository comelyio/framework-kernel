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
use Comely\Framework\AppKernel\Services;
use Comely\Framework\Exception\BootstrapException;
use Comely\Framework\Exception\ConfigException;
use Comely\IO\Cache\Cache;
use Comely\IO\Cipher\Cipher;
use Comely\IO\FileSystem\Disk\Directory;
use Comely\IO\FileSystem\Exception\DiskException;
use Comely\IO\Session\ComelySession;
use Comely\IO\Translator\Translator;
use Comely\Knit\Knit;

/**
 * Class AppKernel
 * @package Comely\Framework
 */
class AppKernel
{
    protected const DIR_CONFIG = "config";
    protected const DIR_CACHE = "cache";
    protected const DIR_COMPILER = "compiler";
    protected const DIR_LANGS = "translator";
    protected const DIR_LOGS = "logs";
    protected const DIR_SESSIONS = "sessions";

    /** @var Config */
    private $config;
    /** @var DateTime */
    private $dateTime;
    /** @var bool */
    private $dev;
    /** @var Directories */
    private $directories;

    private $errorHandler;
    private $memory;
    /** @var Services */
    private $services;

    /**
     * AppKernel constructor.
     * @param array $options
     * @param string $env
     */
    public function __construct(array $options, string $env)
    {
        // Development mode
        $dev = $options["dev"] ?? $dev["development"] ?? null;
        if (!is_bool($dev)) {
            throw new BootstrapException('Option "dev" must be of type boolean');
        }

        $this->dev = $dev;

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

        $this->configure($loadCachedConfig, $env); // Bootstrap configuration

        // Date Time
        $this->dateTime = (new DateTime())
            ->setTimezone($this->config->timeZone());

        // Services
        $this->services = new Services($this);
    }

    /**
     * @param bool $loadCached
     * @param string $env
     * @throws Exception\AppKernelException
     */
    private function configure(bool $loadCached, string $env): void
    {
        if ($this->dev) { // Dev. mode?
            $loadCached = false; // Always load fresh config
        }

        // Load cached?
        if ($loadCached) {
            try { // Check if cached config exists
                $cachedConfigFile = $this->directories->cache()
                    ->file(sprintf('bootstrap.config.%s.php.cache', $env));
            } catch (DiskException $e) {
            }

            if (isset($cachedConfigFile)) {
                try {
                    try {
                        $cachedConfig = $cachedConfigFile->read();
                    } catch (DiskException $e) {
                        throw new ConfigException('Failed to read cached configuration file');
                    }

                    $cachedConfig = unserialize($cachedConfig);
                    if (!$cachedConfig || !$cachedConfig instanceof Config) {
                        throw new ConfigException('Cached configuration file corrupt or incomplete');
                    }

                    // Success
                    $this->config = $cachedConfig;
                } catch (\Exception $e) {
                    trigger_error($e->getMessage(), E_USER_WARNING);

                    // Delete cached file...
                    try {
                        $cachedConfigFile->delete();
                    } catch (DiskException $e) {
                        trigger_error('Failed to delete cached configuration file!', E_USER_WARNING);
                    }
                }
            }
        }

        // Compile fresh configuration
        if (!$this->config) {
            $this->config = new Config($this, $env);
        }

        // Save in cache?
        if ($loadCached) {
            try {
                $cacheDirectory = $this->directories->cache();
                $cacheDirectory->write(
                    sprintf('bootstrap.config.%s.php.cache', $env),
                    base64_encode(serialize($this->config)),
                    false,
                    true
                );
            } catch (DiskException $e) {
                trigger_error(
                    sprintf('Failed to write bootstrap config. file in cache directory. %s', $e->getMessage()),
                    E_USER_WARNING
                );
            }
        }
    }

    /**
     * @return Config
     */
    public function config(): Config
    {
        return $this->config;
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
     * @return bool
     */
    public function dev(): bool
    {
        return $this->dev;
    }

    /**
     * @return Directories
     */
    public function directories(): Directories
    {
        return $this->directories;
    }

    /**
     * @return Services
     */
    public function services(): Services
    {
        return $this->services;
    }

    /**
     * @return Cache
     * @throws \Comely\IO\Cache\Exception\CacheException
     * @throws \Comely\IO\Cache\Exception\ConnectionException
     * @throws \Comely\Kernel\Exception\ServicesException
     */
    public function cache(): Cache
    {
        return $this->services->cache();
    }

    /**
     * @return Cipher
     * @throws \Comely\Kernel\Exception\ServicesException
     */
    public function cipher(): Cipher
    {
        return $this->services->cipher();
    }

    /**
     * @return Translator
     * @throws Exception\AppKernelException
     * @throws \Comely\IO\Translator\Exception\LanguageException
     * @throws \Comely\Kernel\Exception\ServicesException
     */
    public function translator(): Translator
    {
        return $this->services->translator();
    }

    /**
     * @return ComelySession
     * @throws Exception\AppKernelException
     * @throws \Comely\Kernel\Exception\ServicesException
     */
    public function session(): ComelySession
    {
        return $this->services->comelySession();
    }

    /**
     * @return Knit
     * @throws Exception\AppKernelException
     */
    public function knit(): Knit
    {
        return $this->services->knit();
    }
}