<?php


namespace App\Service;


use App\Contract\RateSourceInterface;
use App\Exception\ExchangeRatesUnavailableException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractRateSource implements RateSourceInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    protected function getContent(): string
    {
        try {
            return $this->httpClient->request($this->getMethod(), $this->getUrl())->getContent();
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            throw new ExchangeRatesUnavailableException($e->getMessage());
        }
    }

    abstract public function getMethod(): string;

    abstract public function getUrl(): string;
}
