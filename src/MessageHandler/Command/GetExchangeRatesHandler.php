<?php

namespace App\MessageHandler\Command;

use App\Entity\ExchangeRate;
use App\Message\Command\PopulateExchangeRates;
use App\Repository\ExchangeRateRepository;
use App\Service\RateService;
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

    public function __invoke(PopulateExchangeRates $command)
    {
        // remove all existing rates
        foreach ($this->repository->findAll() as $persistedRate) {
            $this->em->remove($persistedRate);
        }

        // insert new ones
        foreach ($this->rateService->getRates() as $rate) {
            $this->em->persist($rate);
        }
        $this->em->flush();
    }
}
