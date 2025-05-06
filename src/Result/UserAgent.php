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

use Override;

/**
 * Class UserAgent
 *
 * @package Phayne\UAParser\Result
 */
final class UserAgent extends AbstractVersionedSoftware
{
    public string | int | null $major = null;

    public string | int | null $minor = null;

    public string | int | null $patch = null;

    #[Override]
    public function toVersion(): string
    {
        return $this->formatVersion($this->major, $this->minor, $this->patch);
    }
}
