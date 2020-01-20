<?php

namespace App\Service;

use App\Entity\ExchangeRate;
use App\Exception\XmlException;
use DateTimeImmutable;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;

final class CbrRateSource extends AbstractRateSource
{
    public const QUOTE_CURRENCY = 'RUB';

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUrl(): string
    {
        return 'https://www.cbr.ru/scripts/XML_daily.asp';
    }

    protected function getDocumentDate(DOMDocument $doc): DateTimeImmutable
    {
        $xpath = new DOMXPath($doc);
        if (false === $nodeList = $xpath->query('/ValCurs/@Date')) {
            throw new XmlException('Malformed expression to find date element');
        }
        if (0 === $nodeList->length) {
            throw new XmlException('No nodes found in XML');
        }
        $dateAttr = $nodeList->item(0);
        if (null === $dateAttr) {
            throw new XmlException('No date attribute in XML');
        }

        return DateTimeImmutable::createFromFormat('d.m.Y', $dateAttr->nodeValue);
    }

    protected function getQuotes(DOMDocument $doc): DOMNodeList
    {
        $xpath = new DOMXPath($doc);

        return $xpath->query('Valute');
    }

    protected function createExchangeRate(DateTimeImmutable $date, DOMNode $node): ExchangeRate
    {
        $xpath = new DOMXPath($node->ownerDocument);
        $baseCurrency = $this->getNodeValue($node, $xpath, 'CharCode');
        $value = $this->getNodeValue($node, $xpath, 'Value');
        $commaReplaced = str_replace(',', '.', $value);
        $nominal = $this->getNodeValue($node, $xpath, 'Nominal');
        $quote = bcdiv($commaReplaced, $nominal, 8);
        return new ExchangeRate($date, $baseCurrency, self::QUOTE_CURRENCY, $quote);
    }

    protected function getNodeValue(DOMNode $rateNode, DOMXPath $xpath, $nodeName): string
    {
        return $xpath->query($nodeName, $rateNode)->item(0)->nodeValue;
    }
}
