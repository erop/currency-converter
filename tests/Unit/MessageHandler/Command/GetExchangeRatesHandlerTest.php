<?php

namespace App\Tests\Unit\MessageHandler\Command;

use App\Message\Command\GetExchangeRates;
use App\MessageHandler\Command\GetExchangeRatesHandler;
use App\Repository\ExchangeRateRepository;
use App\Service\RateService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetExchangeRatesHandlerTest extends TestCase
{
    public function testGettingExchangeRatesHandlerImplementsMessageHandlerInterface(): void
    {
        $rates = [];
        $rateService = $this->createMock(RateService::class);
        $rateService->expects($this->once())
            ->method('getRates')
            ->willReturn($rates);
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(ExchangeRateRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->willReturn([]);
        $handler = new GetExchangeRatesHandler($rateService, $em, $repository);
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler(new GetExchangeRates());
    }
}
