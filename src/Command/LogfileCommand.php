<?php

/**
 * This file is part of phayne-io/php-user-agent and is proprietary and confidential.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 * @see       https://github.com/phayne-io/php-user-agent for the canonical source repository
 * @copyright Copyright (c) 2024-2025 Phayne Limited. (https://phayne.io)
 */

declare(strict_types=1);

namespace Phayne\UAParser\Command;

use Phayne\UAParser\Exception\InvalidArgumentException;
use Phayne\UAParser\Exception\ReaderException;
use Phayne\UAParser\Parser;
use Phayne\UAParser\Result\Client;
use Phayne\UAParser\Util\Logfile\AbstractReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_map;
use function array_unique;
use function array_values;
use function basename;
use function dirname;
use function file;
use function implode;
use function is_string;
use function json_encode;
use function sprintf;
use function str_repeat;
use function strlen;

/**
 * Class LogfileCommand
 *
 * @package Phayne\UAParser\Command
 */
class LogfileCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('ua-parser:log')
            ->setDescription('Parses the supplied webserver log file.')
            ->addArgument(
                'output',
                InputArgument::REQUIRED,
                'Path to output log file'
            )
            ->addOption(
                'log-file',
                'f',
                InputOption::VALUE_REQUIRED,
                'Path to a webserver log file'
            )
            ->addOption(
                'log-dir',
                'd',
                InputOption::VALUE_REQUIRED,
                'Path to webserver log directory'
            )
            ->addOption(
                'include',
                'i',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Include glob expressions for log files in the log directory',
                ['*.log', '*.log*.gz', '*.log*.bz2']
            )
            ->addOption(
                'exclude',
                'e',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Exclude glob expressions for log files in the log directory',
                ['*error*']
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('log-file') && !$input->getOption('log-dir')) {
            throw InvalidArgumentException::oneOfCommandArguments('log-file', 'log-dir');
        }

        $parser = Parser::create();
        $undefinedClients = [];

        foreach ($this->getFiles($input) as $file) {
            $path = $this->getPath($file);
            $lines = file($path);

            if (empty($lines)) {
                $output->writeln(sprintf('Skipping empty file "%s"', $file->getPathname()));
                $output->writeln('');
                continue;
            }

            $firstLine = reset($lines);

            try {
                $reader = AbstractReader::factory($firstLine);
            } catch (ReaderException) {
                $output->writeln(sprintf('Could not find reader for file "%s"', $file->getPathname()));
                $output->writeln('');
                continue;
            }

            $output->writeln('');
            $output->writeln(sprintf('Analyzing "%s"', $file->getPathname()));

            $count = 1;
            $totalCount = count($lines);

            foreach ($lines as $line) {
                try {
                    $userAgentString = $reader->read($line);
                } catch (ReaderException) {
                    $count = $this->outputProgress($output, 'E', $count, $totalCount);
                    continue;
                }

                $client = $parser->parse($userAgentString);

                $result = $this->getResult($client);
                if ($result !== '.') {
                    $undefinedClients[] = json_encode(
                        [$client->toString(), $userAgentString],
                        JSON_UNESCAPED_SLASHES
                    );
                }

                $count = $this->outputProgress($output, $result, $count, $totalCount);
            }
            $this->outputProgress($output, '', $count - 1, $totalCount, true);
            $output->writeln('');
        }

        $undefinedClients = $this->filter($undefinedClients);


        $outputFile = $input->getArgument('output');
        assert(is_string($outputFile));
        new Filesystem()->dumpFile($outputFile, implode(PHP_EOL, $undefinedClients));

        return 0;
    }

    private function outputProgress(
        OutputInterface $output,
        string $result,
        int $count,
        int $totalCount,
        bool $end = false
    ): int {
        if (($count % 70) === 0 || $end) {
            $formatString = '%s  %' .
                strlen((string)$totalCount) .
                'd / %-' .
                strlen((string)$totalCount) .
                'd (%3d%%)';
            $result = $end ? str_repeat(' ', 70 - ($count % 70)) : $result;
            $output->writeln(sprintf($formatString, $result, $count, $totalCount, $count / $totalCount * 100));
        } else {
            $output->write($result);
        }

        return $count + 1;
    }

    private function getResult(Client $client): string
    {
        if ($client->device->family === 'Spider') {
            return '.';
        }
        if ($client->ua->family === 'Other') {
            return 'U';
        }
        if ($client->os->family === 'Other') {
            return 'O';
        }
        if ($client->device->family === 'Generic Smartphone') {
            return 'S';
        }
        if ($client->device->family === 'Generic Feature Phone') {
            return 'F';
        }

        return '.';
    }

    /** @psalm-return array<array-key, SplFileInfo>|iterable */
    private function getFiles(InputInterface $input): iterable
    {
        $finder = Finder::create();

        $logFile = $input->getOption('log-file');

        if (is_string($logFile)) {
            $finder->append(Finder::create()->in(dirname($logFile))->name(basename($logFile)));
        }

        $logDir = $input->getOption('log-dir');
        if (is_string($logDir)) {
            $dirFinder = Finder::create()->in($logDir);
            array_map([$dirFinder, 'name'], array_map('strval', (array)$input->getOption('include')));
            array_map([$dirFinder, 'notName'], array_map('strval', (array)$input->getOption('exclude')));

            $finder->append($dirFinder);
        }

        return $finder;
    }

    private function filter(array $lines): array
    {
        return array_values(array_unique($lines));
    }

    private function getPath(SplFileInfo $file): string
    {
        $path = match ($file->getExtension()) {
            'gz' => 'compress.zlib://' . $file->getPathname(),
            'bz2' => 'compress.bzip2://' . $file->getPathname(),
            default => $file->getPathname(),
        };

        return $path;
    }
}
