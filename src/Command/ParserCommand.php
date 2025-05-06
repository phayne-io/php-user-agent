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

use Phayne\UAParser\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParserCommand
 *
 * @package Phayne\UAParser\Command
 */
class ParserCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('ua-parser:parse')
            ->setDescription('Parses a user agent string and dumps the results.')
            ->addArgument(
                'user-agent',
                InputArgument::REQUIRED,
                'User agent string to analyze'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userAgent = $input->getArgument('user-agent');
        assert(is_string($userAgent));
        $result = Parser::create()->parse($userAgent);

        $output->writeln(json_encode($result, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
