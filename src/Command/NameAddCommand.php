<?php

namespace App\Command;

use App\Tool\NameAdd;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:name-add',
    description: 'Adding name to every element of tree',
)]
class NameAddCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('category', InputArgument::REQUIRED, 'Category list in JSON')
            ->addArgument('tree', InputArgument::REQUIRED, 'Category tree in JSON')
            ->addArgument('output', InputArgument::REQUIRED, 'Output file')
            ->addOption('maxDepth', 'd', InputOption::VALUE_OPTIONAL, 'Maximum dept of tree process')
            ->addOption('skipOnNull', 's', InputOption::VALUE_NONE, 'Ignore empty names of category')
            ->addOption('localisation','l',InputOption::VALUE_OPTIONAL, "Language of name")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $categoryFile = $input->getArgument('category');
        $treeFile = $input->getArgument('tree');
        $outputFile = $input->getArgument('output');


        $srv = new NameAdd();
        $srv->readFromFiles($categoryFile, $treeFile);

        if($localisation = $input->getOption('localisation')) {
            $srv->setLocalisation($localisation);
        }

        if ($input->getOption('skipOnNull')) {
            $srv->setSkipOnNull(true);
        }

        if($input->getOption('maxDepth')){
            $srv->setMaxDepth($input->getOption('maxDepth'));
        }

        $srv->runAndSaveTo($outputFile, true);

        $io->success('Json File saved to '.$outputFile);

        return Command::SUCCESS;
    }
}
