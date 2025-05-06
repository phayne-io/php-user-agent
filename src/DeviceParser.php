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

use Phayne\UAParser\Result\Device;

/**
 * Class DeviceParser
 *
 * @package Phayne\UAParser
 */
class DeviceParser extends AbstractParser
{
    use ParserFactoryMethods;

    public function parseDevice(string $userAgent): Device
    {
        $device = new Device();

        [$regex, $matches] = self::tryMatch($this->regexes['device_parsers'], $userAgent);

        if ($matches) {
            $device->family = self::multiReplace(
                $regex,
                'device_replacement',
                $matches[1],
                $matches
            ) ?? $device->family;
            $device->brand = self::multiReplace($regex, 'brand_replacement', null, $matches);
            $deviceModelDefault = $matches[1] !== 'Other' ? $matches[1] : null;
            $device->model = self::multiReplace($regex, 'model_replacement', $deviceModelDefault, $matches);
        }

        return $device;
    }
}
