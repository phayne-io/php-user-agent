<?php

/**
 * This file is part of phayne-io/php-user-agent and is proprietary and confidential.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 * @see       https://github.com/phayne-io/php-user-agent for the canonical source repository
 * @copyright Copyright (c) 2024-2025 Phayne Limited. (https://phayne.io)
 */

declare(strict_types=1);

namespace Phayne\UAParser\Result;

use function array_filter;
use function implode;

/**
 * Class AbstractVersionedSoftware
 *
 * @package Phayne\UAParser\Result
 */
abstract class AbstractVersionedSoftware extends AbstractSoftware
{
    abstract public function toVersion(): string;

    public function toString(): string
    {
        return implode(' ', array_filter([$this->family, $this->toVersion()]));
    }

    protected function formatVersion(string | int | null ...$args): string
    {
        return implode('.', array_filter($args, 'is_numeric'));
    }
}
