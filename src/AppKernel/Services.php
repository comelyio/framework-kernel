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
use Comely\IO\Cache\Cache;
use Comely\IO\Cache\Exception\CacheException;
use Comely\IO\Cipher\Cipher;
use Comely\IO\Cipher\Exception\CipherException;
use Comely\IO\Cipher\Keychain\CipherKey;
use Comely\IO\Session\ComelySession;
use Comely\IO\Session\Exception\SessionException;
use Comely\IO\Session\Session;
use Comely\IO\Session\Storage\Disk;
use Comely\IO\Translator\Exception\TranslatorException;
use Comely\IO\Translator\Translator;
use Comely\Kernel\Exception\ServicesException;
use Comely\Knit\Exception\KnitException;
use Comely\Knit\Knit;

/**
 * Class Services
 * @package Comely\Framework\AppKernel
 */
class Services
{
    /** @var AppKernel */
    private $kernel;
    /** @var null|Cache */
    private $cache;
    /** @var null|Cipher */
    private $cipher;
    /** @var null|Session */
    private $session;
    /** @var null|ComelySession */
    private $comelySession;
    /** @var null|Translator */
    private $translator;
    /** @var null|Knit */
    private $knit;

    /**
     * Services constructor.
     * @param AppKernel $kernel
     */
    public function __construct(AppKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return Cache
     * @throws CacheException
     * @throws ServicesException
     * @throws \Comely\IO\Cache\Exception\ConnectionException
     * @throws CacheException
     */
    public function cache(): Cache
    {
        if ($this->cache) { // Already registered?
            return $this->cache;
        }

        $cacheConfig = $this->kernel->config()->services()->cache();
        if (!$cacheConfig instanceof AppKernel\Config\App\Services\Cache) {
            throw ServicesException::ServiceError("cache", 'No configuration found');
        }

        $engine = $cacheConfig->engine();
        switch ($engine) {
            case "redis":
                $engine = Cache::REDIS;
                break;
            case "memcached":
                $engine = Cache::MEMCACHED;
                break;
            default:
                throw ServicesException::ServiceError("cache", 'Bad engine');
        }

        $cache = new Cache();
        $cache->addServer($engine, $cacheConfig->host(), $cacheConfig->port());
        try {
            $cache->connect();
        } catch (CacheException $e) {
            if ($cacheConfig->terminate() === true) {
                throw $e;
            }

            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        $this->cache = $cache;
        return $this->cache;
    }

    /**
     * @return Translator|null
     * @throws ServicesException
     * @throws \Comely\Framework\Exception\AppKernelException
     * @throws \Comely\IO\Translator\Exception\LanguageException
     * @throws TranslatorException
     */
    public function translator(): Translator
    {
        if ($this->translator) { // Already registered?
            return $this->translator;
        }

        $translatorConfig = $this->kernel->config()->services()->translator();
        if (!$translatorConfig instanceof AppKernel\Config\App\Services\Translator) {
            throw ServicesException::ServiceError("translator", 'No configuration found');
        }

        $translator = Translator::getInstance();
        $translator->directory($this->kernel->directories()->langs());
        $translator->fallback($translatorConfig->fallBack());

        // Caching?
        $caching = $translatorConfig->caching() ?? false;
        if ($this->kernel->dev()) {
            $caching = false;
        }

        if ($caching) {
            $translator->cacheDirectory($this->kernel->directories()->cache());
        }

        $this->translator = $translator;
        return $this->translator;
    }

    /**
     * @return Cipher
     * @throws ServicesException
     * @throws CipherException
     */
    public function cipher(): Cipher
    {
        if ($this->cipher) { // Already registered?
            return $this->cipher;
        }

        $cipherConfig = $this->kernel->config()->services()->cipher();
        if (!$cipherConfig instanceof AppKernel\Config\App\Services\Cipher) {
            throw ServicesException::ServiceError("cipher", 'No configuration found');
        }

        $cipher = new Cipher();
        $count = 0;
        foreach ($cipherConfig->keys() as $tag => $words) {
            $key = new CipherKey(hash("sha256", $words));
            $cipher->keychain()->add($tag, $key); // Append keychain

            if ($count === 0) { // First key?
                $cipher->defaultKey($key); // Set as default
            }

            $count++;
        }

        $this->cipher = $cipher;
        return $this->cipher;
    }

    /**
     * @return Session
     * @throws ServicesException
     * @throws \Comely\Framework\Exception\AppKernelException
     * @throws SessionException
     */
    public function sessions(): Session
    {
        if ($this->session) { // Already registered?
            return $this->session;
        }

        $sessionsConfig = $this->kernel->config()->services()->sessions();
        if (!$sessionsConfig instanceof AppKernel\Config\App\Services\Sessions) {
            throw ServicesException::ServiceError("sessions", 'No configuration found');
        }

        $sessions = new Session(new Disk($this->kernel->directories()->sessions()));
        $cookiePath = $sessionsConfig->cookie()->path() ?? null;
        if (!$cookiePath) {
            $cookiePath = "/";
        }
        $cookieDomain = $sessionsConfig->cookie()->domain() ?? null;
        if (!$cookieDomain) {
            $cookieDomain = sprintf('.%s', $this->kernel->config()->project()->domain());
        }

        $sessions->cookies()->expire($sessionsConfig->cookie()->expire())
            ->path($cookiePath)
            ->domain($cookieDomain)
            ->secure($sessionsConfig->cookie()->secure())
            ->httpOnly($sessionsConfig->cookie()->httpOnly());

        $this->session = $sessions;
        $this->comelySession = $sessions->resume(null, $sessionsConfig->cookie()->name());

        return $this->session;
    }

    /**
     * @return ComelySession
     * @throws ServicesException
     * @throws \Comely\Framework\Exception\AppKernelException
     * @throws SessionException
     */
    public function comelySession(): ComelySession
    {
        if ($this->comelySession) { // Already registered?
            return $this->comelySession;
        }

        $this->sessions(); // Load sessions service
        return $this->comelySession();
    }

    /**
     * @return Knit
     * @throws \Comely\Framework\Exception\AppKernelException
     * @throws KnitException
     */
    public function knit(): Knit
    {
        if ($this->knit) {  // Already registered?
            return $this->knit;
        }

        $knit = new Knit();
        $knit->directories()
            ->compiler($this->kernel->directories()->compiler())
            ->caching($this->kernel->directories()->cache());

        $this->knit = $knit;
        return $this->knit;
    }
}