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

use Phayne\UAParser\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

use function array_map;
use function file_get_contents;
use function hash;
use function str_replace;

/**
 * Class Converter
 *
 * @package Phayne\UAParser\Util
 */
final readonly class Converter
{
    public function __construct(
        private string $destination,
        private CodeGenerator $codeGenerator = new CodeGenerator(),
        private Filesystem $fs = new Filesystem()
    ) {
    }

    public function convertFile(string $yamlFile, bool $backupBeforeOverride = true): void
    {
        if (!$this->fs->exists($yamlFile)) {
            throw FileNotFoundException::fileNotFound($yamlFile);
        }

        $this->doConvert(Yaml::parse(file_get_contents($yamlFile)), $backupBeforeOverride);
    }

    public function convertString(string $yamlString, bool $backupBeforeOverride = true): void
    {
        $this->doConvert(Yaml::parse($yamlString), $backupBeforeOverride);
    }

    protected function doConvert(array $regexes, bool $backupBeforeOverride = true): void
    {
        $regexes = $this->sanitizeRegexes($regexes);
        $code = $this->codeGenerator->generateArray($regexes);
        $code = "<?php\nreturn " . $code . "\n";

        $regexesFile = $this->destination . '/regexes.php';

        if ($backupBeforeOverride && $this->fs->exists($regexesFile)) {
            $currentHash = hash('sha512', file_get_contents($regexesFile));
            $futureHash = hash('sha512', $code);

            if ($futureHash === $currentHash) {
                return;
            }

            $backupFile = $this->destination . '/regexes-' . $currentHash . '.php';
            $this->fs->copy($regexesFile, $backupFile);
        }

        $this->fs->dumpFile($regexesFile, $code);
    }

    private function sanitizeRegexes(array $regexes): array
    {
        foreach ($regexes as $groupName => $group) {
            $regexes[$groupName] = array_map([$this, 'sanitizeRegex'], $group);
        }

        return $regexes;
    }

    private function sanitizeRegex(array $regex): array
    {
        $regex['regex'] = '@' . str_replace('@', '\@', $regex['regex']) . '@';

        if (isset($regex['regex_flag'])) {
            $regex['regex'] .= $regex['regex_flag'];
        }

        unset($regex['regex_flag']);

        return $regex;
    }
}
