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

namespace Comely\Framework\AppKernel\Config;

use Comely\Framework\Exception\ConfigException;

/**
 * Class Database
 * @package Comely\Framework\AppKernel\Config
 * @method string driver()
 * @method string host()
 * @method null|int port()
 * @method string name()
 * @method null|string username()
 * @method null|string password()
 */
class Database extends AbstractConfigNode
{
    /** @var string */
    private $driver;
    /** @var string */
    private $host;
    /** @var null|int */
    private $port;
    /** @var string */
    private $name;
    /** @var null|string */
    private $username;
    /** @var null|string */
    private $password;

    /**
     * Database constructor.
     * @param string $tag
     * @param array $db
     * @throws ConfigException
     */
    public function __construct(string $tag, array $db)
    {
        // Driver
        $driver = $db["driver"] ?? null;
        if (!is_string($driver)) {
            throw ConfigException::DatabaseError($tag, 'Invalid driver name');
        }

        $this->driver = strtolower($driver);

        // Hostname
        $host = $db["host"] ?? null;
        if (!is_string($host)) {
            throw ConfigException::DatabaseError($tag, 'Invalid database hostname');
        }

        $this->host = $host;

        // Port
        $port = $db["port"] ?? null;
        if (!is_int($port) && !is_null($port)) {
            throw ConfigException::DatabaseError($tag, 'Port must be a integer or NULL');
        }

        $this->port = $port;

        // Name
        $name = $db["name"] ?? null;
        if (!is_string($name)) {
            throw ConfigException::DatabaseError($tag, 'Invalid database name');
        }

        $this->name = $name;

        // Username
        $username = $db["username"] ?? null;
        if (!is_string($username) && !is_null($username)) {
            throw ConfigException::DatabaseError($tag, 'Username must be a string or NULL');
        }

        $this->username = $username;

        // Password
        $password = $db["password"] ?? null;
        if (!is_string($password) && !is_null($password)) {
            throw ConfigException::DatabaseError($tag, 'Password must be a string or NULL');
        }

        $this->password = $password;
    }
}