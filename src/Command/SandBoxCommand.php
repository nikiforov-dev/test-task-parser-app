<?php

namespace App\Command;

use App\DependencyInjection\InjectionTrait\ParserInjectionTrait;
use App\Parser\Adapter\RbcAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SandBoxCommand extends Command
{
    use ParserInjectionTrait;

    protected function configure()
    {
        $this->setName("app:sandbox");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->parser->parseSource(RbcAdapter::init(40));

        $io->success(intdiv(12, 10));

        return Command::SUCCESS;
    }
}