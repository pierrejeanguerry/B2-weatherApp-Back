<?php

namespace App\Command;

use App\Entity\Station;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-station-token',
    description: 'Add a short description for your command',
)]
class GenerateStationTokenCommand extends Command
{
    public function __construct(private EntityManagerInterface $manager, private StationRepository $repo)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg = $input->getArgument('arg1');

        if (!$arg) {
            $arg = 1;
        }
        $io->title('List of archives items');
        $tableHeaders = ['id','token'];
        $tableRows = [];
        for ($nb = 0; $nb < $arg; $nb++) {
            $stations[$nb] = new Station;
            $stations[$nb]->setToken(bin2hex(random_bytes(32)));
            $tableRows[] = [$stations[$nb]->getId(), $stations[$nb]->getToken()];
            $this->manager->persist($stations[$nb]);
        }
        $this->manager->flush();
        $io->table($tableHeaders, $tableRows);

        return Command::SUCCESS;
    }
}
