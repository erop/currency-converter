<?php

namespace App\Tests\Unit\Service;

use App\Contract\RateSourceInterface;
use App\Service\RateService;
use App\Service\RateSourceFactory;
use PHPUnit\Framework\TestCase;

class RateServiceTest extends TestCase
{
    public function testService(): void
    {
        $rateSource = $this->createMock(RateSourceInterface::class);
        $factory = $this->createMock(RateSourceFactory::class);
        $factory->expects($this->once())
            ->method('getRateSource')
            ->willReturn($rateSource);
        $service = new RateService($factory);
        $service->getRates();
    }
}
