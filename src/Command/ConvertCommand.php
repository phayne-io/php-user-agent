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

use Override;
use Phayne\UAParser\Util\Converter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConvertCommand
 *
 * @package Phayne\UAParser\Command
 */
class ConvertCommand extends Command
{
    public function __construct(private readonly string $resourceDirectory, private readonly string $defaultYamlFile)
    {
        parent::__construct('ua-parser:convert');
    }

    #[Override]
    protected function configure(): void
    {
        $this
            ->setName('ua-parser:convert')
            ->setDescription('Converts an existing regexes.yaml file to a regexes.php file.')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'Path to the regexes.yaml file',
                $this->defaultYamlFile
            )
            ->addOption(
                'no-backup',
                null,
                InputOption::VALUE_NONE,
                'Do not backup the previously existing file'
            )
        ;
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        assert(is_string($file));
        $noBackup = $input->getOption('no-backup');
        assert(is_bool($noBackup));
        $this->getConverter()->convertFile($file, $noBackup);

        return self::SUCCESS;
    }

    private function getConverter(): Converter
    {
        return new Converter($this->resourceDirectory);
    }
}
