<?php

namespace App\Command;

use App\Message\Command\GetExchangeRates;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class GetRatesCommand extends Command
{
    protected static $defaultName = 'app:get-rates';
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * GetRatesCommand constructor.
     * @param MessageBusInterface $messageBus
     */
    public function __construct(MessageBusInterface $messageBus)
    {
        parent::__construct();
        $this->messageBus = $messageBus;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Get exchange rates from the bank selected and store it in database')
            ->setHelp('Use this command to retrieve exchange rate from the bank defined in RATE_SOURCE env var');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start getting rates...');
        $this->messageBus->dispatch(new GetExchangeRates());
        $output->writeln('Rates retrieved and stored');

        return 0;
    }
}
