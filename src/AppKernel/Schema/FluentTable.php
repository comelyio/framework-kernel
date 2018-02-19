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

namespace Comely\Framework\AppKernel\Schema;

use Comely\Fluent\Database\Table;
use Comely\Framework\AppKernel;

/**
 * Class FluentTable
 * @package Comely\Framework\AppKernel\Schema
 */
abstract class FluentTable extends Table
{
    /** @var AppKernel */
    protected $app;
    /** @var AppKernel\Memory */
    protected $memory;

    /**
     * @throws \Comely\Framework\Exception\BootstrapException
     */
    public function callback()
    {
        $this->app = AppKernel::getInstance();
        $this->memory = $this->app->memory();
    }
}