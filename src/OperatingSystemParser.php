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

use Phayne\UAParser\Result\OperatingSystem;

/**
 * Class OperatingSystemParser
 *
 * @package Phayne\UAParser
 */
class OperatingSystemParser extends AbstractParser
{
    use ParserFactoryMethods;

    public function parseOperatingSystem(string $userAgent): OperatingSystem
    {
        $os = new OperatingSystem();

        [$regex, $matches] = self::tryMatch($this->regexes['os_parsers'], $userAgent);

        if ($matches) {
            $os->family = self::multiReplace($regex, 'os_replacement', $matches[1], $matches) ?? $os->family;
            $os->major = self::multiReplace($regex, 'os_v1_replacement', $matches[2], $matches);
            $os->minor = self::multiReplace($regex, 'os_v2_replacement', $matches[3], $matches);
            $os->patch = self::multiReplace($regex, 'os_v3_replacement', $matches[4], $matches);
            $os->patchMinor = self::multiReplace($regex, 'os_v4_replacement', $matches[5], $matches);
        }

        return $os;
    }
}
