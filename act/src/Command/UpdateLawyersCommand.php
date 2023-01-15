<?php

namespace App\Command;

use App\Service\LdapService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateLawyersCommand extends Command
{
    protected static $defaultName = 'update:lawyers';

    private $ldapService;

    public function __construct(string $name = null,LdapService $ldapService)
    {
        $this->ldapService = $ldapService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('scope1', null, InputOption::VALUE_NONE, '1er scope')
            ->addOption('scope2', null, InputOption::VALUE_NONE, '2eme scope')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('scope1')) {
            $this->ldapService->lawyerDirectory();
        }elseif ($input->getOption('scope2')){
            $this->ldapService->loopResults2();
        }

//        $this->ldapService->loopResults1();

        $io->success('l\'annuaire a été mis a jour .');

        return 0;
    }
}
