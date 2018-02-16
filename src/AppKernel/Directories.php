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

use Comely\Framework\Exception\AppKernelException;
use Comely\IO\FileSystem\Disk\Directory;

/**
 * Class Directories
 * @package Comely\Framework\AppKernel
 */
class Directories
{
    /** @var null|Directory */
    private $root;
    /** @var null|Directory */
    private $cache;
    /** @var null|Directory */
    private $compiler;
    /** @var null|Directory */
    private $logs;

    /**
     * @return Directory
     * @throws AppKernelException
     */
    public function root(): Directory
    {
        if (!$this->root) {
            throw new AppKernelException('App root directory is not set');
        }

        return $this->root;
    }

    /**
     * @return Directory
     * @throws AppKernelException
     */
    public function cache(): Directory
    {
        if (!$this->cache) {
            throw new AppKernelException('App cache directory is not set');
        }

        return $this->cache;
    }

    /**
     * @return Directory
     * @throws AppKernelException
     */
    public function compiler(): Directory
    {
        if (!$this->compiler) {
            throw new AppKernelException('App compiler directory is not set');
        }

        return $this->compiler;
    }

    /**
     * @return Directory
     * @throws AppKernelException
     */
    public function logs(): Directory
    {
        if (!$this->logs) {
            throw new AppKernelException('App logs directory is not set');
        }

        return $this->logs;
    }

    /**
     * @param string $prop
     * @param Directory $directory
     */
    public function set(string $prop, Directory $directory): void
    {
        if (!property_exists($this, $prop)) {
            throw new AppKernelException('Invalid directory property');
        }

        // No reset root directory
        if ($prop === "root" && $this->root) {
            throw new AppKernelException('App root directory already defined');
        }

        // Permissions check
        switch ($prop) {
            case "cache":
            case "compiler":
            case "logs":
                if (!$directory->permissions()->read) {
                    throw new AppKernelException(sprintf('Directory "%s" is not readable', $prop));
                } elseif (!$directory->permissions()->write) {
                    throw new AppKernelException(sprintf('Directory "%s" is not writable', $prop));
                }
                break;
        }

        $this->$prop = $directory;
    }
}