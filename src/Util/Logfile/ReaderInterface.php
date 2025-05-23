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

/**
 * Interface ReaderInterface
 *
 * @package Phayne\UAParser\Util\Logfile
 */
interface ReaderInterface
{
    public function test(string $line): bool;

    public function read(string $line): string;
}
