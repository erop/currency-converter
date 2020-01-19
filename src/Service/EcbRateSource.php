<?php


namespace App\Service;


use App\Entity\ExchangeRate;
use App\Exception\ProcessXmlException;
use App\Exception\XmlSchemaModifiedException;
use DateTimeImmutable;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;

final class EcbRateSource extends AbstractRateSource
{

    public const BASE_CURRENCY = 'EUR';
    public const DEFAULT_NAMESPACE_URI = 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref';

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

    /**
     * @param DOMDocument $doc
     * @return DateTimeImmutable
     * @throws Exception
     */
    private function getDocumentDate(DOMDocument $doc): DateTimeImmutable
    {
        $xpath = $this->prepareXPath($doc, 'd');
        $dateNode = $xpath->query('//d:Cube/d:Cube');
        $dateValue = $dateNode->item(0)->attributes->getNamedItem('time')->nodeValue;
        return new DateTimeImmutable($dateValue);
    }

    /**
     * @param DOMDocument $doc
     * @param $prefix
     *
     * @return DOMXPath
     */
    private function prepareXPath(DOMDocument $doc, string $prefix): DOMXPath
    {
        $xpath = new DOMXPath($doc);
        if ( ! $xpath->registerNamespace($prefix, self::DEFAULT_NAMESPACE_URI)) {
            throw new ProcessXmlException(
                sprintf('Could not register default namespace %s', self::DEFAULT_NAMESPACE_URI)
            );
        }
        return $xpath;
    }

    private function getQuotes(DOMDocument $doc): \DOMNodeList
    {
        $xpath = $this->prepareXPath($doc, 'd');
        if ( ! $quotes = $xpath->query('//d:Cube/d:Cube/d:Cube')) {
            throw new XmlSchemaModifiedException('ECB modified its XML file schema: Could not find quotes themselves');
        }
        return $quotes;
    }

    private function getQuoteAttributeValue(DOMNode $quote, string $attrName): string
    {
        return $quote->attributes->getNamedItem($attrName)->nodeValue;
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUrl(): string
    {
        return 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    }

    /**
     * @param DateTimeImmutable $date
     * @param $quote
     * @return ExchangeRate
     */
    private function createExchangeRate(DateTimeImmutable $date, $quote): ExchangeRate
    {
        return new ExchangeRate(
            $date,
            self::BASE_CURRENCY,
            $this->getQuoteAttributeValue($quote, 'currency'),
            $this->getQuoteAttributeValue($quote, 'rate')
        );
    }
}
