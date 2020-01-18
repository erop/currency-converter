<?php

namespace App\Tests\Unit\Service;

use App\Contract\RateSourceInterface;
use App\Exception\RateSourceNotFoundException;
use App\Service\AbstractRateSource;
use App\Service\CbrRateSource;
use App\Service\EcbRateSource;
use App\Service\RateSourceFactory;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class RateSourceFactoryTest extends TestCase
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @param string $rateSourceAbbr
     * @param string $rateSourceClass
     * @dataProvider getRateSources
     */
    public function testFactoryReturnsCorrectRateSource(string $rateSourceAbbr, string $rateSourceClass): void
    {
        $factory = new RateSourceFactory($rateSourceAbbr, $this->httpClient);
        $rateSource = $factory->getRateSource();
        $this->assertInstanceOf(AbstractRateSource::class, $rateSource);
        $this->assertInstanceOf($rateSourceClass, $rateSource);
        $this->assertInstanceOf(RateSourceInterface::class, $rateSource);
    }

    public function testGivenWrongRateSourceAbbreviationShouldThrowException(): void
    {
        $this->expectException(RateSourceNotFoundException::class);
        $factory = new RateSourceFactory('not_existing_rate_source', $this->httpClient);
        $factory->getRateSource();
    }

    public function getRateSources(): Generator
    {
        yield ['ECB', EcbRateSource::class];
        yield ['CBR', CbrRateSource::class];
    }

    protected function setUp()
    {
        $this->httpClient = new HttpClient();
    }
}
