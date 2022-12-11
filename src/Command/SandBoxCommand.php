<?php

namespace App\Command;

use App\DependencyInjection\InjectionTrait\ArticleRepositoryInjectionTrait;
use App\DependencyInjection\InjectionTrait\ParserInjectionTrait;
use App\Parser\Adapter\RbcAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SandBoxCommand extends Command
{
    use ParserInjectionTrait;
    use ArticleRepositoryInjectionTrait;

    protected function configure()
    {
        $this->setName('app:sandbox');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $objects = $this->parser->parseSource(RbcAdapter::init(100));

        $this->articleRepository->createArticlesFromRbcDTOArray($objects);
        var_dump($this->articleRepository->getLastArticles(15));

        return Command::SUCCESS;
    }
}
