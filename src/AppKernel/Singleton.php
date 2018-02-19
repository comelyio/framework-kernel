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
use Comely\Framework\Exception\AppKernelException;
use Comely\Framework\Exception\BootstrapException;
use Comely\IO\Cache\Cache;
use Comely\IO\Cache\Exception\CacheException;
use Comely\IO\Cipher\Cipher;
use Comely\IO\Cipher\Exception\CipherException;
use Comely\IO\Session\ComelySession;
use Comely\IO\Session\Exception\SessionException;
use Comely\IO\Translator\Exception\TranslatorException;
use Comely\IO\Translator\Translator;
use Comely\Knit\Exception\KnitException;
use Comely\Knit\Knit;

/**
 * Class Singleton
 * @package Comely\Framework\AppKernel
 */
abstract class Singleton
{
    /** @var null|AppKernel */
    protected static $instance;

    /**
     * @return AppKernel
     * @throws BootstrapException
     */
    final public static function getInstance(): AppKernel
    {
        if (!self::$instance) {
            throw new BootstrapException('AppKernel not bootstrapped');
        }

        return self::$instance;
    }

    /**
     * @return AppKernel
     * @throws BootstrapException
     */
    final public static function getKernel(): AppKernel
    {
        return self::getInstance();
    }

    /**
     * @return Cache
     * @throws BootstrapException
     * @throws AppKernelException
     * @throws CacheException
     */
    final public static function getCache(): Cache
    {
        return self::getInstance()->cache();
    }

    /**
     * @return Cipher
     * @throws BootstrapException
     * @throws BootstrapException
     * @throws AppKernelException
     * @throws CipherException
     */
    final public static function getCipher(): Cipher
    {
        return self::getInstance()->cipher();
    }

    /**
     * @return ComelySession
     * @throws AppKernelException
     * @throws BootstrapException
     * @throws SessionException
     */
    final public static function getSession(): ComelySession
    {
        return self::getInstance()->session();
    }

    /**
     * @return Translator
     * @throws AppKernelException
     * @throws BootstrapException
     * @throws TranslatorException
     */
    public static function getTranslator(): Translator
    {
        return self::getInstance()->translator();
    }

    /**
     * @return Knit
     * @throws AppKernelException
     * @throws BootstrapException
     * @throws KnitException
     */
    public static function getKnit(): Knit
    {
        return self::getInstance()->knit();
    }
}