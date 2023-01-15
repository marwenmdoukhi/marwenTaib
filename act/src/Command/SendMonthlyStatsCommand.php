<?php

namespace App\Command;

use App\Controller\Api\StatsAction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Controller\Api\ApiActController;

class SendMonthlyStatsCommand extends Command
{
    protected static $defaultName = 'send:monthlyStats';

    /**
     * @var StatsAction
     */
    private StatsAction $statsAction;


    /**
     * @param string|null $name
     * @param StatsAction $statsAction
     */
    public function __construct(string $name = null, StatsAction $statsAction)
    {
        parent::__construct($name);
        $this->statsAction = $statsAction;
    }


    protected function configure()
    {
        $this->setDescription('Send Monthlu stats, every 01 of the month at 10:00');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->statsAction->__invoke();
        $io->success('Statistiques envoyées avec succès');

        return 0;
    }
}
