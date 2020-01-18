<?php


namespace App\Service;


use App\Contract\RateSourceInterface;
use Symfony\Component\HttpClient\HttpClient;

abstract class AbstractRateSource implements RateSourceInterface
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}
