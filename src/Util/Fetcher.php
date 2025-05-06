<?php

/**
 * This file is part of phayne-io/php-user-agent and is proprietary and confidential.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 * @see       https://github.com/phayne-io/php-user-agent for the canonical source repository
 * @copyright Copyright (c) 2024-2025 Phayne Limited. (https://phayne.io)
 */

declare(strict_types=1);

namespace Phayne\UAParser\Util;

use Composer\CaBundle\CaBundle;
use Phayne\UAParser\Exception\FetcherException;

use function error_get_last;
use function error_reporting;
use function file_get_contents;
use function get_resource_type;
use function is_resource;
use function stream_context_create;

/**
 * Class Fetcher
 *
 * @package Phayne\UAParser\Util
 */
class Fetcher
{
    private string $resourceUri = 'https://raw.githubusercontent.com/ua-parser/uap-core/main/regexes.yaml';

    /** @var resource */
    private $streamContext;

    public function __construct($streamContext = null)
    {
        if (is_resource($streamContext) && get_resource_type($streamContext) === 'stream-context') {
            $this->streamContext = $streamContext;
        } else {
            $this->streamContext = stream_context_create(
                [
                    'ssl' => [
                        'verify_peer' => true,
                        'verify_depth' => 10,
                        'cafile' => CaBundle::getSystemCaRootBundlePath(),
                        'CN_match' => 'www.github.com',
                        'disable_compression' => true,
                    ]
                ]
            );
        }
    }

    public function fetch(): string
    {
        $level = error_reporting(0);
        $result = file_get_contents($this->resourceUri, false, $this->streamContext);
        error_reporting($level);

        if ($result === false) {
            $error = error_get_last();
            throw FetcherException::httpError($this->resourceUri, $error['message'] ?? 'Undefined error');
        }

        return $result;
    }
}
