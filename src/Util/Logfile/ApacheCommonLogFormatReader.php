<?php

/**
 * This file is part of phayne-io/php-user-agent and is proprietary and confidential.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 * @see       https://github.com/phayne-io/php-user-agent for the canonical source repository
 * @copyright Copyright (c) 2024-2025 Phayne Limited. (https://phayne.io)
 */

declare(strict_types=1);

namespace Phayne\UAParser\Util\Logfile;

use Override;

/**
 * Class ApacheCommonLogFormatReader
 *
 * @package Phayne\UAParser\Util\Logfile
 */
class ApacheCommonLogFormatReader extends AbstractReader
{
    #[Override]
    protected function getRegex(): string
    {
        return '@^
            (?:\S+)                                                 # IP
            \s+
            (?:\S+)
            \s+
            (?:\S+)
            \s+
            \[(?:[^:]+):(?:\d+:\d+:\d+) \s+ (?:[^\]]+)\]            # Date/time
            \s+
            \"(?:\S+)\s(?:.*?)                                      # Verb
            \s+
            (?:\S+)\"                                               # Path
            \s+
            (?:\S+)                                                 # Response
            \s+
            (?:\S+)                                                 # Length
            \s+
            (?:\".*?\")                                             # Referrer
            \s+
            \"(?P<userAgentString>.*?)\"                            # User Agent
        $@x';
    }
}
