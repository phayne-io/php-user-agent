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
use Phayne\UAParser\Util\Fetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand
 *
 * @package Phayne\UAParser\Command
 */
class UpdateCommand extends Command
{
    public function __construct(private readonly string $resourceDirectory)
    {
        parent::__construct('ua-parser:update');
    }

    #[Override]
    protected function configure(): void
    {
        $this
            ->setName('ua-parser:update')
            ->setDescription('Fetches an updated YAML file for ua-parser and overwrites the current PHP file.')
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
        $fetcher = new Fetcher();
        $converter = new Converter($this->resourceDirectory);

        $converter->convertString($fetcher->fetch(), !$input->getOption('no-backup'));

        return self::SUCCESS;
    }
}
