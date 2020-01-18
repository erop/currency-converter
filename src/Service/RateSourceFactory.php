<?php


namespace App\Service;


use App\Contract\RateSourceInterface;
use App\Exception\RateSourceNotFoundException;
use Symfony\Component\HttpClient\HttpClient;

class RateSourceFactory
{
    /**
     * @var string
     */
    private $rateSource;
    /**
     * @var HttpClient
     */
    private $httpClient;


    /**
     * RateSourceFactory constructor.
     * @param string $rateSource
     * @param HttpClient $httpClient
     */
    public function __construct(string $rateSource, HttpClient $httpClient)
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
