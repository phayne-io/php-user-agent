<?php

/**
 * This file is part of phayne-io/php-user-agent and is proprietary and confidential.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 * @see       https://github.com/phayne-io/php-user-agent for the canonical source repository
 * @copyright Copyright (c) 2024-2025 Phayne Limited. (https://phayne.io)
 */

declare(strict_types=1);

namespace Phayne\UAParser;

use function preg_match;
use function preg_replace_callback;
use function trim;

/**
 * Class AbstractParser
 *
 * @package Phayne\UAParser
 */
abstract class AbstractParser
{
    public function __construct(protected array $regexes = [])
    {
    }

    protected static function tryMatch(array $regexes, string $userAgent): array
    {
        foreach ($regexes as $regex) {
            if (preg_match($regex['regex'], $userAgent, $matches)) {
                $defaults = [
                    1 => 'Other',
                    2 => null,
                    3 => null,
                    4 => null,
                    5 => null,
                ];

                return [$regex, $matches + $defaults];
            }
        }

        return [null, null];
    }

    protected static function multiReplace(array $regex, string $key, ?string $default, array $matches): ?string
    {
        if (! isset($regex[$key])) {
            return self::emptyStringToNull($default);
        }

        $replacement = preg_replace_callback(
            '|\$(?P<key>\d)|',
            static function ($m) use ($matches) {
                return $matches[$m['key']] ?? '';
            },
            $regex[$key]
        );

        return self::emptyStringToNull($replacement);
    }

    private static function emptyStringToNull(?string $string): ?string
    {
        $string = trim($string ?? '');

        return $string === '' ? null : $string;
    }
}
