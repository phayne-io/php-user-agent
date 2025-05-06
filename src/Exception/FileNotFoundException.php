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

use InvalidArgumentException;

/**
 * Class FileNotFoundException
 *
 * @package Phayne\UAParser\Exception
 */
final class FileNotFoundException extends InvalidArgumentException
{
    public static function fileNotFound(string $file): self
    {
        return new self(sprintf('File "%s" does not exist', $file));
    }

    public static function customRegexFileNotFound(string $file): self
    {
        return new self(
            sprintf(
                'ua-parser cannot find the custom regexes file you supplied ("%s"). Please make sure you have the correct path.', // phpcs:ignore
                $file
            )
        );
    }

    public static function defaultFileNotFound(string $file): self
    {
        return new self(
            sprintf(
                'Please download the "%s" file before using ua-parser by running "php bin/uaparser ua-parser:update"',
                $file
            )
        );
    }
}
