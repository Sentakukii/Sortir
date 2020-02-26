<?php

namespace App\Command;

use App\Services\EventService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UpdateEventCommand extends Command
{
    protected static $defaultName = 'app:update-event';
    private $eventService;

    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
        $this->eventService = new EventService();
    }

    protected function configure()
    {
        $this
            ->setDescription('update State for the different events')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Event Update',
            '============',
            '',
        ]);


        $output->write( $this->eventService->updateStateEvent($this->container->get('doctrine')->getManager()));


        $output->write(' finish ');
        return 0;
    }
}
