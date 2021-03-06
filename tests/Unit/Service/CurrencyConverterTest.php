<?php

namespace App\Tests\Unit\Service;

use App\Entity\ExchangeRate;
use App\Message\Query\GetCurrencyConverted;
use App\Repository\ExchangeRateRepository;
use App\Service\CurrencyConverter;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    public function testConversionForExactExchangeRate(): void
    {
        $request = new GetCurrencyConverted('EUR', 'USD', '123.45');
        $repository = $this->createMock(ExchangeRateRepository::class);
        $repository->expects(self::once())
            ->method('findOneBy')
            ->with(['baseCurrency' => $request->getFromCurrency(), 'quoteCurrency' => $request->getToCurrency()])
            ->willReturn(new ExchangeRate(new \DateTimeImmutable('today'), 'EUR', 'USD', '1.1'));
        $response = (new CurrencyConverter($repository))->process($request);
        self::assertEquals('135.80', $response->getToAmount());
    }

    public function testConversionForInvertedExchangeRate(): void
    {
        $request = new GetCurrencyConverted('EUR', 'USD', '123.45');
        $repository = $this->createMock(ExchangeRateRepository::class);
        $repository->expects(self::at(0))
            ->method('findOneBy')
            ->with(['baseCurrency' => 'EUR', 'quoteCurrency' => 'USD'])
            ->willReturn(null);
        $repository->expects(self::at(1))
            ->method('findOneBy')
            ->with(['baseCurrency' => 'USD', 'quoteCurrency' => 'EUR'])
            ->willReturn(new ExchangeRate(new \DateTimeImmutable('today'), 'USD', 'EUR', '0.90909090'));
        $response = (new CurrencyConverter($repository))->process($request);
        self::assertEquals('135.80', $response->getToAmount());
    }

    public function testConversionOfAdjacentCurrencyPairs(): void
    {
        $request = new GetCurrencyConverted('GBP', 'USD', '123.45');
        $repository = $this->createMock(ExchangeRateRepository::class);
        $repository->expects(self::at(0))
            ->method('findOneBy')
            ->with(['baseCurrency' => 'GBP', 'quoteCurrency' => 'USD'])
            ->willReturn(null);
        $repository->expects(self::at(1))
            ->method('findOneBy')
            ->with(['baseCurrency' => 'USD', 'quoteCurrency' => 'GBP'])
            ->willReturn(null);

        $firstPair = new ExchangeRate(new \DateTimeImmutable('today'), 'EUR', 'GBP', '0.84');
        $secondPair = new ExchangeRate(new \DateTimeImmutable('today'), 'EUR', 'USD', '1.11');
        $repository->expects(self::at(2))
            ->method('findOneBy')
            ->with(['quoteCurrency' => 'GBP'])
            ->willReturn($firstPair);
        $repository->expects(self::at(3))
            ->method('findOneBy')
            ->with(['quoteCurrency' => 'USD'])
            ->willReturn($secondPair);
        $response = (new CurrencyConverter($repository))->process($request);
        self::assertEquals('163.13', $response->getToAmount());
    }
}
