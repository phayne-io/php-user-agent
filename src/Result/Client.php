<?php

/**
 * This file is part of phayne-io/php-user-agent and is proprietary and confidential.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 * @see       https://github.com/phayne-io/php-user-agent for the canonical source repository
 * @copyright Copyright (c) 2024-2025 Phayne Limited. (https://phayne.io)
 */

declare(strict_types=1);

namespace Phayne\UAParser\Result;

/**
 * Class Client
 *
 * @package Phayne\UAParser\Result
 */
class Client extends AbstractClient
{
    public UserAgent $ua;

    public OperatingSystem $os;

    public Device $device;

    public function __construct(public string $originalUserAgent)
    {
    }

    public function toString(): string
    {
        return sprintf('%s / %s', $this->ua->toString(), $this->os->toString());
    }
}
