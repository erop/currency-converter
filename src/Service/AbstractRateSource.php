<?php


namespace App\Service;


use App\Contract\RateSourceInterface;
use App\Entity\ExchangeRate;
use App\Exception\ExchangeRatesUnavailableException;
use DateTimeImmutable;
use DOMDocument;
use DOMNode;
use Exception;
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

    /**
     * @return ExchangeRate[]
     * @throws Exception
     */
    public function getRates(): array
    {
        $xml = $this->getContent();
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $date = $this->getDocumentDate($doc);
        $quotes = $this->getQuotes($doc);
        $rates = [];
        foreach ($quotes as $quote) {
            $rates[] = $this->createExchangeRate($date, $quote);
        }
        return $rates;
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

    abstract protected function getDocumentDate(DOMDocument $doc);

    abstract protected function getQuotes(DOMDocument $doc);

    abstract protected function createExchangeRate(DateTimeImmutable $date, DOMNode $node);

}
