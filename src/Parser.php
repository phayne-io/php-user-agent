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

use Phayne\UAParser\Result\Client;

/**
 * Class Parser
 *
 * @package Phayne\UAParser
 */
class Parser extends AbstractParser
{
    use ParserFactoryMethods;

    private DeviceParser $deviceParser;

    private OperatingSystemParser $operatingSystemParser;

    private UserAgentParser $userAgentParser;

    public function __construct(array $regexes)
    {
        parent::__construct($regexes);
        $this->deviceParser = new DeviceParser($this->regexes);
        $this->operatingSystemParser = new OperatingSystemParser($this->regexes);
        $this->userAgentParser = new UserAgentParser($this->regexes);
    }

    public function parse(string $userAgent, array $jsParseBits = []): Client
    {
        $client = new Client($userAgent);

        $client->ua = $this->userAgentParser->parseUserAgent($userAgent, $jsParseBits);
        $client->os = $this->operatingSystemParser->parseOperatingSystem($userAgent);
        $client->device = $this->deviceParser->parseDevice($userAgent);

        return $client;
    }
}
