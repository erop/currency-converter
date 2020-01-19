<?php

namespace App\Service;

use App\Contract\RateSourceInterface;
use App\Exception\RateSourceNotFoundException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RateSourceFactory
{
    /**
     * @var string
     */
    private $rateSource;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * RateSourceFactory constructor.
     */
    public function __construct(string $rateSource, HttpClientInterface $httpClient)
    {
        $this->rateSource = $rateSource;
        $this->httpClient = $httpClient;
    }

    public function getRateSource(): RateSourceInterface
    {
        switch ($this->rateSource) {
            case 'ECB':
                return new EcbRateSource($this->httpClient);
            case 'CBR':
                return new CbrRateSource($this->httpClient);
            default:
                throw new RateSourceNotFoundException('Could not instantiate rate source service');
        }
    }
}
