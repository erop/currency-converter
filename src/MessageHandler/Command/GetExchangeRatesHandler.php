<?php

namespace App\MessageHandler\Command;

use App\Exception\Domain\ExchangeRatesDuplicationException;
use App\Message\Command\GetExchangeRates;
use App\Repository\ExchangeRateRepository;
use App\Service\RateService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetExchangeRatesHandler implements MessageHandlerInterface
{
    /**
     * @var RateService
     */
    private $rateService;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ExchangeRateRepository
     */
    private $repository;

    public function __construct(
        RateService $rateService,
        EntityManagerInterface $em,
        ExchangeRateRepository $repository
    ) {
        $this->rateService = $rateService;
        $this->em = $em;
        $this->repository = $repository;
    }

    public function __invoke(GetExchangeRates $command)
    {
        $today = new DateTimeImmutable('today');
        $persistedRates = $this->repository->findBy(['date' => $today]);

        if (!empty($persistedRates)) {
            $message = sprintf('Exchange rates for %s already exist in database', $today->format('Y-m-d'));
            throw new ExchangeRatesDuplicationException($message);
        }

        foreach ($this->rateService->getRates() as $rate) {
            $this->em->persist($rate);
        }
        $this->em->flush();
    }
}
