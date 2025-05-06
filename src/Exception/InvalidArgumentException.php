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

use InvalidArgumentException as SplInvalidArgumentException;

/**
 * Class InvalidArgumentException
 *
 * @package Phayne\UAParser\Exception
 */
class InvalidArgumentException extends SplInvalidArgumentException
{
    public static function oneOfCommandArguments(string ...$args): self
    {
        return new self(
            sprintf('One of the command arguments "%s" is required', implode('", "', $args))
        );
    }
}
