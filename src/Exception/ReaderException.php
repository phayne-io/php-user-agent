<?php

/**
 * This file is part of phayne-io/php-user-agent and is proprietary and confidential.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 * @see       https://github.com/phayne-io/php-user-agent for the canonical source repository
 * @copyright Copyright (c) 2024-2025 Phayne Limited. (https://phayne.io)
 */

declare(strict_types=1);

namespace Phayne\UAParser\Exception;

use DomainException;

use function sprintf;

/**
 * Class ReaderException
 *
 * @package Phayne\UAParser\Exception
 */
class ReaderException extends DomainException
{
    public static function userAgentParserError(string $line): self
    {
        return new self(sprintf('Cannot extract user agent string from line "%s"', $line));
    }

    public static function readerNotFound(string $line): self
    {
        return new self(sprintf('Cannot find reader that can handle "%s"', $line));
    }
}
