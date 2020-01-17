<?php

namespace App\Tests\Unit\Service;

use App\Contract\RateSourceInterface;
use App\Service\CbrRateSource;
use App\Service\EcbRateSource;
use App\Service\RateSourceFactory;
use Generator;
use PHPUnit\Framework\TestCase;

class RateSourceFactoryTest extends TestCase
{
    /**
     * @param string $rateSourceAbbr
     * @param string $rateSourceClass
     * @dataProvider getRateSources
     */
    public function testFactoryReturnsCorrectRateSource(string $rateSourceAbbr, string $rateSourceClass): void
    {
        $factory = new RateSourceFactory($rateSourceAbbr);
        $rateSource = $factory->getRateSource();
        $this->assertInstanceOf($rateSourceClass, $rateSource);
        $this->assertInstanceOf(RateSourceInterface::class, $rateSource);
    }

    public function getRateSources(): Generator
    {
        yield ['ECB', EcbRateSource::class];
        yield ['CBR', CbrRateSource::class];
    }
}
