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
 * Class FetcherException
 *
 * @package Phayne\UAParser\Exception
 */
class FetcherException extends DomainException
{
    public static function httpError(string $resource, string $error): self
    {
        return new self(
            sprintf('Could not fetch HTTP resource "%s": %s', $resource, $error)
        );
    }
}
