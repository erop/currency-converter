<?php

namespace App\Tests\Unit\MessageHandler\Command;

use App\Entity\ExchangeRate;
use App\Exception\Domain\ExchangeRatesDuplicationException;
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
        $rates = [new ExchangeRate(new \DateTimeImmutable('today'), 'EUR', 'USD', '1.1')];
        $rateService = $this->createMock(RateService::class);
        $rateService->expects($this->once())
            ->method('getRates')
            ->willReturn($rates);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
            ->method('persist');
        $repository = $this->createMock(ExchangeRateRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->willReturn([]);
        $handler = new GetExchangeRatesHandler($rateService, $em, $repository);
        $this->assertInstanceOf(MessageHandlerInterface::class, $handler);
        $handler(new GetExchangeRates());
        // todo review all tests and complete them
    }

    public function testThrowExceptionOnExistingRates(): void
    {
        $this->expectException(ExchangeRatesDuplicationException::class);
        $rateService = $this->createMock(RateService::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(ExchangeRateRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->willReturn($this->createMock(ExchangeRate::class));
        $handler = new GetExchangeRatesHandler($rateService, $em, $repository);
        $handler(new GetExchangeRates());
    }
}
