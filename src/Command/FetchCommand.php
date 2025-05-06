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

use Phayne\UAParser\Util\Fetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use function assert;

/**
 * Class FetchCommand
 *
 * @package Phayne\UAParser\Command
 */
class FetchCommand extends Command
{
    public function __construct(private readonly string $defaultYamlFile)
    {
        parent::__construct('ua-parser:fetch');
    }

    protected function configure(): void
    {
        $this
            ->setName('ua-parser:fetch')
            ->setDescription('Fetches an updated YAML file for ua-parser.')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'regexes.yaml output file',
                $this->defaultYamlFile
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        assert(is_string($file));

        new Filesystem()->dumpFile($file, new Fetcher()->fetch());

        return self::SUCCESS;
    }
}
