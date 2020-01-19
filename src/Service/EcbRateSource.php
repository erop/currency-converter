<?php


namespace App\Service;


use App\Entity\ExchangeRate;
use App\Exception\ProcessXmlException;
use App\Exception\XmlException;
use App\Exception\XmlSchemaModifiedException;
use DateTimeImmutable;
use DOMDocument;
use DOMNamedNodeMap;
use DOMNode;
use DOMXPath;
use Exception;

final class EcbRateSource extends AbstractRateSource
{

    public const BASE_CURRENCY = 'EUR';
    public const DEFAULT_NAMESPACE_URI = 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref';

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUrl(): string
    {
        return 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    }

    /**
     * @param DOMDocument $doc
     * @return DateTimeImmutable| null
     * @throws XmlException
     * @throws Exception
     * @psalm-suppress MixedInferredReturnType
     */
    protected function getDocumentDate(DOMDocument $doc): ?DateTimeImmutable
    {
        $xpath = $this->prepareXPath($doc, 'd');
        if (false === $nodeList = $xpath->query('//d:Cube/d:Cube')) {
            throw new ProcessXmlException('Incorrect XPath expression');
        }
        if (0 === $nodeList->count()) {
            throw new XmlSchemaModifiedException('ECB modified its XML file schema: Could not find node with date');
        }
        return DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $nodeList->item(0)->attributes->getNamedItem('time')->nodeValue
        );
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

    protected function getQuotes(DOMDocument $doc): \DOMNodeList
    {
        $xpath = $this->prepareXPath($doc, 'd');
        if (false === $quotes = $xpath->query('//d:Cube/d:Cube/d:Cube')) {
            throw new XmlSchemaModifiedException('ECB modified its XML file schema: Could not find quotes themselves');
        }
        return $quotes;
    }

    /**
     * @param DateTimeImmutable $date
     * @param $quote
     *
     * @return ExchangeRate
     */
    protected function createExchangeRate(DateTimeImmutable $date, DOMNode $quote): ExchangeRate
    {
        if (null === $quoteCurrency = $this->getQuoteAttributeValue($quote, 'currency')) {
            throw new XmlSchemaModifiedException('Could not find quote currency');
        }
        if (null === $rate = $this->getQuoteAttributeValue($quote, 'rate')) {
            throw new XmlSchemaModifiedException('Could not find quote');
        }
        return new ExchangeRate(
            $date,
            self::BASE_CURRENCY,
            $quoteCurrency,
            $rate
        );
    }

    /**
     * @param DOMNode $quote
     * @param string $attrName
     * @psalm-suppress MixedReturnStatement
     * @return string|null
     */
    private function getQuoteAttributeValue(DOMNode $quote, string $attrName): ?string
    {
        /** @var DOMNamedNodeMap $attributes */
        /** @var DOMNode $attribute */
        if ((null !== $attributes = $quote->attributes)
            && (null !== $attribute = $attributes->getNamedItem($attrName))
            && (null !== $value = $attribute->nodeValue)) {
            return $value;
        }
        return null;
    }
}
