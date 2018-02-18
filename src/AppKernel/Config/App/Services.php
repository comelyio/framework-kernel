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

namespace Comely\Framework\AppKernel\Config\App;

use Comely\Framework\AppKernel\Config\AbstractConfigNode;
use Comely\Framework\AppKernel\Config\App\Services\Cache;
use Comely\Framework\AppKernel\Config\App\Services\Cipher;
use Comely\Framework\AppKernel\Config\App\Services\Mailer;
use Comely\Framework\AppKernel\Config\App\Services\Translator;

/**
 * Class Services
 * @package Comely\Framework\AppKernel\Config\App
 * @method null|Cache cache()
 * @method null|Cipher cipher()
 * @method null|Translator translator()
 */
class Services extends AbstractConfigNode
{
    /** @var null|Cache */
    private $cache;
    /** @var null|Cipher */
    private $cipher;
    /** @var null|Cipher */
    private $mailer;
    /** @var null|Translator */
    private $translator;

    /**
     * Services constructor.
     * @param array $services
     */
    public function __construct(array $services)
    {
        // Cache
        $cache = $services["cache"] ?? null;
        if (is_array($cache)) {
            $this->cache = new Cache($cache);
        }

        // Cipher
        $cipher = $services["cipher"] ?? null;
        if (is_array($cipher)) {
            $this->cipher = new Cipher($cipher);
        }

        // Mailer
        $mailer = $services["mailer"] ?? null;
        if (is_array($mailer)) {
            $this->mailer = new Mailer($mailer);
        }

        // Translator
        $translator = $services["translator"] ?? null;
        if (is_array($translator)) {
            $this->translator = new Translator($translator);
        }
    }
}