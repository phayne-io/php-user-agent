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

use Phayne\UAParser\Result\UserAgent;

use function sprintf;
use function str_contains;

/**
 * Class UserAgentParser
 *
 * @package Phayne\UAParser
 */
class UserAgentParser extends AbstractParser
{
    use ParserFactoryMethods;

    public function parseUserAgent(string $userAgent, array $jsParseBits = []): UserAgent
    {
        $ua = new UserAgent();

        if (isset($jsParseBits['js_user_agent_family']) && $jsParseBits['js_user_agent_family']) {
            $ua->family = $jsParseBits['js_user_agent_family'];
            $ua->major = $jsParseBits['js_user_agent_v1'];
            $ua->minor = $jsParseBits['js_user_agent_v2'];
            $ua->patch = $jsParseBits['js_user_agent_v3'];
        } else {
            [$regex, $matches] = self::tryMatch($this->regexes['user_agent_parsers'], $userAgent);

            if ($matches) {
                $ua->family = self::multiReplace($regex, 'family_replacement', $matches[1], $matches) ?? $ua->family;
                $ua->major = self::multiReplace($regex, 'v1_replacement', $matches[2], $matches);
                $ua->minor = self::multiReplace($regex, 'v2_replacement', $matches[3], $matches);
                $ua->patch = self::multiReplace($regex, 'v3_replacement', $matches[4], $matches);
            }
        }

        if (isset($jsParseBits['js_user_agent_string'])) {
            $jsUserAgentString = $jsParseBits['js_user_agent_string'];
            if (
                str_contains($jsUserAgentString, 'Chrome/') &&
                str_contains($userAgent, 'chromeframe')
            ) {
                $override = $this->parseUserAgent($jsUserAgentString);
                $family = $ua->family;
                $ua->family = 'Chrome Frame';
                if ($ua->major !== null) {
                    $ua->family .= sprintf(' (%s %s)', $family, $ua->major);
                }
                $ua->major = $override->major;
                $ua->minor = $override->minor;
                $ua->patch = $override->patch;
            }
        }

        return $ua;
    }
}
