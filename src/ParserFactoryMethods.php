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

use Phayne\UAParser\Exception\FileNotFoundException;

use function dirname;

use const DIRECTORY_SEPARATOR;

/**
 * Trait ParserFactoryMethods
 *
 * @package Phayne\UAParser
 */
trait ParserFactoryMethods
{
    public static ?string $defaultFile = null;

    public static function create(?string $file = null): self
    {
        return $file ? self::createCustom($file) : self::createDefault();
    }

    protected static function createDefault(): self
    {
        return self::createInstance(
            self::getDefaultFile(),
            [FileNotFoundException::class, 'defaultFileNotFound']
        );
    }

    protected static function createCustom(string $file): self
    {
        return self::createInstance(
            $file,
            [FileNotFoundException::class, 'customRegexFileNotFound']
        );
    }

    private static function createInstance(string $file, callable $exceptionFactory): self
    {
        if (!file_exists($file)) {
            throw $exceptionFactory($file);
        }

        static $map = [];
        if (!isset($map[$file])) {
            $map[$file] = include $file;
        }

        return new self($map[$file]);
    }

    protected static function getDefaultFile(): string
    {
        return self::$defaultFile ?: dirname(__DIR__) . '/resources' . DIRECTORY_SEPARATOR . 'regexes.php';
    }
}
