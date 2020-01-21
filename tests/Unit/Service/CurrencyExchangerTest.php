<?php

namespace App\Tests\Unit\Service;

use App\Dto\ExchangeRequest;
use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use App\Service\CurrencyConverter;
use PHPUnit\Framework\TestCase;

class CurrencyExchangerTest extends TestCase
{
    public function testConversionForExactExchangeRate(): void
    {
        $request = new ExchangeRequest('EUR', 'USD', '123.45');
        $repository = $this->createMock(ExchangeRateRepository::class);
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['baseCurrency' => $request->getFromCurrency(), 'quoteCurrency' => $request->getToCurrency()])
            ->willReturn(new ExchangeRate(new \DateTimeImmutable('today'), 'EUR', 'USD', '1.1'));
        $exchanger = new CurrencyConverter($repository);
        $response = $exchanger->process($request);
        $this->assertEquals('135.80', $response->getToAmount());
    }

    public function testConversionForInvertedExchangeRate(): void
    {
        $request = new ExchangeRequest('EUR', 'USD', '123.45');
        $repository = $this->createMock(ExchangeRateRepository::class);
        $repository->expects($this->at(0))
            ->method('findOneBy')
            ->with(['baseCurrency' => 'EUR', 'quoteCurrency' => 'USD'])
            ->willReturn(null);
        $repository->expects($this->at(1))
            ->method('findOneBy')
            ->with(['baseCurrency' => 'USD', 'quoteCurrency' => 'EUR'])
            ->willReturn(new ExchangeRate(new \DateTimeImmutable('today'), 'USD', 'EUR', '0.90909090'));
        $exchanger = new CurrencyConverter($repository);
        $response = $exchanger->process($request);
        $this->assertEquals('135.80', $response->getToAmount());
    }
}
