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

namespace Comely\Framework\AppKernel\Config\App\Services;

use Comely\Framework\AppKernel\Config\AbstractConfigNode;
use Comely\Framework\Exception\ConfigException;

/**
 * Class Sessions
 * @package Comely\Framework\AppKernel\Config\App\Services
 * @method null|string encrypt()
 * @method SessionsCookie cookie()
 */
class Sessions extends AbstractConfigNode
{
    /** @var string */
    private $encrypt;
    /** @var SessionsCookie */
    private $cookie;

    /**
     * Sessions constructor.
     * @param array $sessions
     * @throws ConfigException
     */
    public function __construct(array $sessions)
    {
        // Encrypt sessions?
        $encrypt = $sessions["encrypt"] ?? $sessions["cipher"] ?? null;
        if (!is_string($encrypt) && !is_null($encrypt)) {
            throw ConfigException::PropError('services.sessions', 'Property "encrypt" must be string or NULL');
        }

        $this->encrypt = $encrypt;

        // Cookie
        $cookie = $sessions["cookie"] ?? null;
        if (!is_array($cookie)) {
            throw ConfigException::PropError('services.sessions', 'Required node "cookie" not configured');
        }

        $this->cookie = new SessionsCookie($cookie);
    }
}