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

namespace Comely\Framework\AppKernel\Schema;

use Comely\Fluent\ORM\Model;
use Comely\Framework\AppKernel;

/**
 * Class FluentModel
 * @package Comely\Framework\AppKernel\Schema
 */
abstract class FluentModel extends Model
{
    /** @var null|AppKernel */
    protected $app;

    /**
     * Set $app prop with AppKernel instance
     * @throws \Comely\Framework\Exception\BootstrapException
     */
    public function onLoad()
    {
        $this->app = AppKernel::getInstance();
    }

    /**
     * Clear $app property
     */
    public function onSleep()
    {
        $this->app = null;
    }

    /**
     * Set $app prop with AppKernel instance
     * @throws \Comely\Framework\Exception\BootstrapException
     */
    public function onWakeup()
    {
        $this->app = AppKernel::getInstance();
    }
}