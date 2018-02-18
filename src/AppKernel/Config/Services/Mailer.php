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
 * Class Mailer
 * @package Comely\Framework\AppKernel\Config\App\Services
 * @method string agent()
 * @method string senderName()
 * @method string senderEmail()
 * @method null|MailerSMTP smtp()
 */
class Mailer extends AbstractConfigNode
{
    /** @var string */
    private $agent;
    /** @var string */
    private $senderName;
    /** @var string */
    private $senderEmail;
    /** @var MailerSMTP */
    private $smtp;

    /**
     * Mailer constructor.
     * @param array $mailer
     * @throws ConfigException
     */
    public function __construct(array $mailer)
    {
        // Agent
        $agent = $mailer["agent"] ?? null;
        if (!is_string($agent)) {
            throw ConfigException::PropError('services.mailer.smtp', 'Property "agent" must be a string');
        }

        $this->agent = $agent;

        // Sender ID
        $senderName = $mailer["sender"]["name"] ?? null;
        $senderEmail = $mailer["sender"]["email"] ?? null;
        if (!is_string($senderName) || !$senderName) {
            throw ConfigException::PropError('services.mailer.smtp.sender', 'Property "name" must be a string');
        } elseif (!is_string($senderEmail) || !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
            throw ConfigException::PropError('services.mailer.smtp.sender', 'Property "email" must be a string');
        }

        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;

        // SMTP
        $smtp = $mailer["smtp"] ?? null;
        if (is_array($smtp)) {
            $this->smtp = new MailerSMTP($smtp);
        }
    }
}