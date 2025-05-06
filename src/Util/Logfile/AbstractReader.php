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

use Phayne\UAParser\Exception\ReaderException;

/**
 * Class AbstractReader
 *
 * @package Phayne\UAParser\Util\Logfile
 */
abstract class AbstractReader implements ReaderInterface
{
    private static array $readers = [];

    public static function factory(string $line): ReaderInterface
    {
        foreach (static::getReaders() as $reader) {
            if ($reader->test($line)) {
                return $reader;
            }
        }

        throw ReaderException::readerNotFound($line);
    }

    private static function getReaders(): array
    {
        if (static::$readers) {
            return static::$readers;
        }

        static::$readers[] = new ApacheCommonLogFormatReader();

        return static::$readers;
    }

    public function test(string $line): bool
    {
        $matches = $this->match($line);

        return isset($matches['userAgentString']);
    }

    public function read(string $line): string
    {
        $matches = $this->match($line);

        if (!isset($matches['userAgentString'])) {
            throw ReaderException::userAgentParserError($line);
        }

        return $matches['userAgentString'];
    }

    protected function match(string $line): array
    {
        if (preg_match($this->getRegex(), $line, $matches)) {
            return $matches;
        }

        return [];
    }

    abstract protected function getRegex();
}
