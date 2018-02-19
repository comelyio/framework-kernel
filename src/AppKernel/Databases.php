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
use Comely\IO\Database\Database;
use Comely\IO\Database\Exception\DatabaseException;

/**
 * Class Databases
 * @package Comely\Framework\AppKernel
 */
class Databases
{
    /** @var AppKernel */
    private $kernel;
    /** @var array */
    private $dbs;

    /**
     * Databases constructor.
     * @param AppKernel $kernel
     */
    public function __construct(AppKernel $kernel)
    {
        $this->kernel = $kernel;
        $this->dbs = [];
    }

    /**
     * @param string $tag
     * @return Database
     * @throws AppKernelException
     * @throws DatabaseException
     */
    public function get(string $tag = "primary"): Database
    {
        if (array_key_exists($tag, $this->dbs)) {
            return $this->dbs[$tag]; // Already exists
        }

        $dbConfig = $this->kernel->config()->databases();
        $dbConfig = $dbConfig[$tag] ?? null;
        if (!$dbConfig instanceof AppKernel\Config\Database) {
            throw new AppKernelException(sprintf('A database with tag "%s" is not configured', $tag));
        }

        switch ($dbConfig->driver()) {
            case "mysql":
                $driver = Database::MYSQL;
                break;
            case "sqlite":
                $driver = Database::SQLITE;
                break;
            case "pgsql":
            case "postgresql":
                $driver = Database::PGSQL;
                break;
            default:
                throw new AppKernelException(sprintf('Invalid driver for database "%s"', $tag));
        }

        $server = Database::Server($driver)
            ->host($dbConfig->host());

        if ($dbConfig->port()) {
            $server->port($dbConfig->port());
        }

        if ($dbConfig->username()) {
            $username = $dbConfig->username() ?? "";
            $password = $dbConfig->password() ?? "";
            $server->credentials($username, $password);
        }

        $db = $server->connect();
        $this->dbs[$tag] = $db;

        return $db;
    }
}